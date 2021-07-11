<?php
/**
 * 
 * DressAPI
 * @version 1.0
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @url https://dressapi.com
 * 
 * 
 * This class contains the base Controller which manages all the tables in the DB 
 * and from which a specific controller can be inherited
 *  
 */

namespace DressApi\Modules\Base;

require_once __DIR__ . '/config.php';

use Exception;
use DressApi\Core\DBMS\CMySqlDB as CDB;
use DressApi\Core\User\CUser;
use DressApi\Core\Request\CRequest;
use DressApi\Core\Response\CResponse;

use DressApi\Core\DBMS\CSqlComposerBase;

class CBaseController extends CDB
{
    // Table (structure cachable)
    protected ?array $db_tables = null;      // all the tables in the DB and all fields data

    protected string $table;         // name of current table (derived from current module) 

    protected string $items_view = '*'; // fields of the table to display 

    protected array $excluded_controllers = [];      // the tables that should not be exposed

    protected string $method;         // GET, PUT, PATCH, POST, OPTIONS, HEAD, DELETE 

    protected array $bind_params_values = [];
    protected array $bind_params_types = [];

    protected $model; // CBaseModel or sons

    protected array $related_field_names = [];

    // optional objects
    protected $cache; // CCache or sons
    protected $user;  // CUser  or sons

    protected $request;  // CRequest
    protected $response; // CResponse

    /**
     * Constructor
     *
     * @param string $table the real table of DB (normally is used by derived classes of BaseController)
     *
     * @return void
     */
    public function __construct(CUser $user = null, CRequest $request, CResponse $response, $cache = null)
    {
        $this->user = $user;
        $this->request  = $request;
        $this->response = $response;

        $this->cache = $cache;

        $this->response->setStatusCode(CResponse::HTTP_STATUS_BAD_REQUEST);

        $this->table = $this->request->getModule();

        // Verify if the method (GET,POST,DELETE,...) is implemented
        $this->method = $this->request->getMethod();
        if (!method_exists($this, 'exec' . ucfirst(strtolower($this->method))))
            throw new Exception("Method ".strtoupper($this->method)." not avaiable");            

        $this->related_field_names = [];

        $this->bind_params_values = [];
        $this->bind_params_types = [];

        $this->format = DEFAULT_FORMAT_OUTPUT;
        try
        {
            $this->_setAllTables(); // Legge e memorizza tutte le tabelle del DB

            $module = $this->table;

            // Check if the instantiated controller is a superclass of DressApi\Modules\Base\CBaseController
            $have_a_specific_module = is_subclass_of($this,'DressApi\Modules\Base\CBaseController');

            if (!$have_a_specific_module)
            {
                // set a table of DB with "Base" Module
                $this->setTable($module); // Tables of the DB
            }

            $model = self::GetModuleModel();
            $this->model = new $model($this->table, $this->db_tables[$this->table] ?? null);

            $this->setItemsView(); // Fields to display, default '*' that is all

            $this->response->setStatusCode(CResponse::HTTP_STATUS_OK);
            $this->response->setMessageError(); // no errors

        }
        catch (Exception $ex)
        {
            $this->response->setMessageError($ex->getMessage());
            throw new Exception($ex->getMessage());
        }
    }


    /**
     * Return the name of required module (default: 'Base')
     * 
     * @return string the name of the required module
     */
    public static function GetModule(): string
    {
        $module_name = 'Base';
        $filters =  ((isset($_SERVER['QUERY_STRING'])) ? ($_SERVER['QUERY_STRING']) : (''));

        if ($filters)
        {
            if (strpos($filters, '/') === false)
                $module_name = ucfirst($filters);  // the unique element of filter is the module/table
            else
            {
                $filt = explode('/', $filters);
                $module_name = ucfirst(array_shift($filt)); // the first element of filter is the module/table
            }
        }

        if (strstr($module_name, '-'))
            list($module_name,) = explode('-', $module_name);

        return $module_name;
    }


    /**
     * Return the fullname of the class more appropriated
     * 
     * @param string $class_type class type (normally 'Controller' or 'Model')
     *
     * @return string the fullname of class required
     */
    public static function GetModuleElement(string $class_type): string
    {
        $module = self::GetModule();
        $file = realpath(__DIR__ . '/../' . $module . '/C' . $module . $class_type . '.class.php');

        if (file_exists($file))
            $ret = 'DressApi\Modules\\' . $module . '\\C' . $module . $class_type;
        else
            $ret = 'DressApi\Modules\\Base\\CBase' . $class_type;

        return $ret;
    }


    /**
     * Include the Controller class more appropriated and return the fullname
     * 
     * @return string the fullname of class Controller required
     */
    public static function GetModuleController(): string
    {
        return self::GetModuleElement('Controller');
    }


    /**
     * Include the Model class more appropriated and return the fullname
     * 
     * @return string the fullname of class Model required
     */
    public static function GetModuleModel(): string
    {
        return self::GetModuleElement('Model');
    }


    /**
     * Import from DB all table names and update excluded/included table/controllers
     * 
     */
    private function _setAllTables(): void
    {

        if ($this->cache)
        {
            $this->cache->setArea('structures');
            $this->db_tables = $this->cache->get('db_tables');
        }

        if ($this->db_tables === null)
        {
            $this->db_tables = [];
            $this->getAllTables($this->db_tables);
            if ($this->cache)
                $this->cache->set('db_tables', $this->db_tables);
        }

        $this->setExcludedControllers();
    }


    /**
     * Delete all controllers derived from DB tables
     * 
     * @param array $controllers list of controllers derived from the DB to be excluded
     * 
     */
    public function setExcludedControllers(array $controllers = []): void
    {
        $this->excluded_controllers = $controllers;
        if (isset($controllers) && count($controllers) > 0)
            $this->db_tables = array_filter(
                $this->db_tables,
                function ($k) use ($controllers)
                {
                    return !in_array($k, $controllers);
                },
                ARRAY_FILTER_USE_KEY
            );
    }


    /**
     * Set table name and keep all table fields with types
     * NOTE: if $table have "-rel" the foreign key with id the keys will be replaced with their names
     * 
     * @param $table table of DB or Controller
     * @return void
     * 
     * @throw Exception if table or the field of order not exist 
     * 
     * @see setMapFieldNames()
     * 
     */
    protected function setTable(string $table): void
    {
        if (isset($this->db_tables) && !isset($this->db_tables[$table])) // NOT exists
            throw new Exception("Module ".ucfirst($table)." not exist");

        $order_by = $this->request->getOrderBy();
        if (isset($order_by[0]) && !isset($this->db_tables[$table][strtolower($order_by[0])]))
            throw new Exception("The field to sort the list with does not exist in the table");                        
        
        $this->setDBTable($table); // Tabelle di default nel DB
    }


    /**
     * For POST, PUT, PATCH methods add some limitations as min or max value or a specific rule with regular expression 
     * 
     * @param array $field array contains min,max,rule values
     * @param string $table table of DB or Controller (if the value is null or not declared, the table name is the table request)
     * @return void
     * 
     * @see setItemsRequired()
     * 
     * @example
     *   $controller = CBaseController::GetModuleController();
     *   $rest = new $controller( );
     *   $rest->addItemRequired( 'freq', ['rule'=>'/[daily|weekly|monthly]/'],'page' );
     *   $rest->addItemRequired( 'age', ['min'=>16, 'max'=35], 'student' );
     * 
     */
    public function addItemRequired(string $name, array $options, string $table = null): void
    {
        if (($table == $this->table || $table === null) && in_array($this->method, ['POST', 'PUT', 'PATCH']))
        {
            $this->model->setRequired(  $name, 
                                        true, 
                                        ((isset($options['min'])) ? ($options['min']) : (null)), 
                                        ((isset($options['max'])) ? ($options['max']) : (null))
                                    );
            if (isset($options['rule']))
                $this->model->setRule($name, $options['rule']);
        }
    }


    /**
     * For POST, PUT, PATCH methods add some limitations as min or max value or a specific rule with regular expression 
     * 
     * @param array $list array contains per each table the relative limitations of single items
     * @return void
     * 
     * @see addItemRequired() for a single table this is prefered method
     * 
     * @example
     *   $controller = CBaseController::GetModuleController();
     *   $rest = new $controller( );
     *   // resource is the table/controller, the internal_code and the name are items with a limitated quantity of characters  
     *   $rest->setItemRequired( ['student' => [ ['name'=>'age','min'=>16, 'max'=>35], ['name'=>'vote','min'=>18, 'max'=>30] ]] );
     */
    public function setItemsRequired(array $list): void
    {
        if (isset($list[$this->table]) && in_array($this->method, array('POST', 'PUT', 'PATCH')))
        {
            foreach ($list[$this->table] as $field)
            {
                $this->model->setRequired($field['name'], true, ((isset($field['min'])) ? ($field['min']) : (null)), ((isset($field['max'])) ? ($field['max']) : (null)));
                if (isset($field['rule']))
                    $this->model->setRule($field['name'], $field['rule']);
            }
        }
    }


    /**
     * For the GET method, it replaces the index of the foreign key with the field name 
     * ($related_item_name) indicated in the related table ($table)
     * 
     * @param string $related_item_name The name of the related table
     * @param string $table The name of the field in the related table (if the value is null or not declared, the table name is the table request)
     * @return void
     * 
     * @see setRelatedFieldNames() to indicate all fields of all tables
     * 
     * @example
     *   $controller = CBaseController::GetModuleController();
     *   $rest = new $controller( );
     *   $rest->addRelatedFieldName( 'name', 'preference' ); // id_preference => [table:field] => preference:name
     */
    public function addRelatedFieldName(string $related_item_name, ?string $table = null): void
    {
        if ($table === null)
            $table = $this->table;
        if ($table == '*' || ($this->db_tables !== NULL && isset($this->db_tables[$table])))
            $this->related_field_names[$table] = $related_item_name;
    }


    /**
     * For the GET method, it replaces the index of the foreign key with the value of related field 
     * ($related_item_name) indicated in the related table ($table)
     * 
     * @param array  $list array containing for each table the name of the field 
     *                     of the correlated table with which the index will have to be replaced
     * @return void
     * 
     * @see addRelatedFieldName() for a single table this is prefered method
     * 
     * @example
     *   $controller = CBaseController::GetModuleController();
     *   $rest = new $controller( );
     *   $rest->setRelatedFieldNames( ['user'=>'email','*'=>'name'] );
     */
    public function setRelatedFieldNames(array $list): void
    {
        $this->related_field_names = [];
        if (isset($list) && is_array($list))
            foreach ($list as $table => $related_item_name)
                if ($table == '*' || ($this->db_tables !== NULL && isset($this->db_tables[$table])))
                    $this->related_field_names[$table] = $related_item_name;
    }


    /**
     * @param ?array $vitems array containing for each table the name of the field 
     *                   of the correlated table with which the index will have to be replaced
     * @return void
     * 
     */
    protected function setItemsView(?array $vitems = null): void
    {
        if ($vitems === null && isset($this->db_tables[$this->table]))
            $this->items_view = implode(',', $this->model->getListItems()); // , *, "name,surname,private_email"';   
    }


    /**
     * Invalidating the http cache
     *
     * @return void
     */
    protected function _revalidateHttpCache(): void
    {
        $seconds_to_cache = 0; // 0=nessuna cache
        $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        if ($seconds_to_cache > 0)
            header("Cache-Control: max-age=$seconds_to_cache");
        else
            header("Cache-Control: no-cache, must-revalidate");
    }


    /**
     * Set the conditions according to the input filters
     *
     * @return string string containing the conditions of an SQL query created with input filters
     */
    protected function getConditionsByFilters(): string
    {
        $conditions = '';

        $this->bind_params_values = [];
        $this->bind_params_types = [];

        $filters = $this->request->getFilters();

        if (isset($filters) && is_array($filters) && count($filters))
        {
            $start = true;
            if (isset($filters['all']))
            {
                list($operator, $value) = $filters['all'];
                unset($filters['all']);

                foreach ($this->db_tables[$this->table] as $name => $col)
                    if (strstr($col['type'], 'VARCHAR'))
                    {
                        if ($start)
                            $start = false;
                        else
                            $conditions .= ' OR';
                        $conditions .= " (`$name` LIKE ?)";
                        $this->bind_params_values[] = "%$value%";
                        $this->bind_params_types[] = $col['type'];
                    }
            }

            if ($filters !== null)
            {
                foreach ($filters as $name => $filter)
                {
                    list($operator, $value) = $filter;

                    if ($conditions != '')
                        $conditions .= ' AND';
                    if ($name == str_replace('[table]', $this->table, ITEM_ID) && $operator == '=')
                    {
                        if ($value == '*')
                            continue;
                        else
                        {
                            $conditions .= " $name IN (?)";
                            $this->bind_params_values[] = $value;
                        }
                    }
                    else
                    {
                        $conditions .= " ";
                        switch ($operator)
                        {
                            case '~':
                                $conditions .= " $name LIKE ?";
                                $this->bind_params_values[] = "%$value%";
                                break;
                            default:
                                $conditions .= " $name$operator?";
                                $this->bind_params_values[] = $value;
                                break;
                        }
                    }
                    $this->bind_params_types[] = $this->db_tables[$this->table][$name]['type'];
                }
            }
        }
        return trim($conditions);
    }


    /**
     * Import all necessary conditions based on filters and any additional conditions
     *
     * @return string string containing the conditions of an SQL query created with input filters
     *                and any other additional conditions
     */
    protected function getConditions(): string
    {
        $conditions = $this->getConditionsByFilters();
        $additional_conditions = $this->model->getAdditionalConditions();
        if ($additional_conditions)
        {
            if ($conditions)
                $conditions = "($conditions) AND ($additional_conditions)";
            else
                $conditions = $additional_conditions;
        }
        return $conditions;
    }


    /**
     * Insert a new record into a DB table
     *
     * @return bool true if the operation was successful, false in all other cases
     */
    protected function _insertDB(): bool
    {
        $ret = false;

        $params = $this->request->getParameters();
        $field_id_user = str_replace('[related_table]',USER_TABLE,RELATED_TABLE_ID);
        
        // Force the id_user to current id user value
        if (isset($this->db_tables[$this->table][$field_id_user]))
            $params[$field_id_user] = (($this->user!==null)?($this->user->getId()):(NULL));

        // Force eventual creation_date to current_date
        if (isset($this->db_tables[$this->table][CREATION_DATE]))
        {
            if ($this->db_tables[$this->table][$field_id_user]['type']=='DATETIME')
                $params[CREATION_DATE] = $this->getCurrentDateTime();
            else
                $params[CREATION_DATE] = $this->getCurrentDate();
        }

        if (isset($params))
        {
            $this->model->setFilters($params);
            $this->model->checkValid(true);

            $item_types = [];
            foreach ($params as $key => $val)
                if (isset($this->db_tables[$this->table][$key]['type']))
                    $item_types[] = $this->db_tables[$this->table][$key]['type'];
                else
                    throw new Exception('Item ' . $key . ' not valid');

            $ret = $this->insertRecord($this->table, $params, $item_types);
        }

        return $ret;
    }


    /**
     * Modify a record into a DB table
     *
     * @return bool true if the operation was successful, false in all other cases
     */
    protected function _updateDB(bool $all_params_required = true): bool
    {
        $this->response->setStatusCode(CResponse::HTTP_STATUS_BAD_REQUEST);

        $ret = false;
        try
        {
            $this->model->setFilters($this->request->getParameters());
            $this->model->checkValid($all_params_required);

            $conditions = $this->getConditions();

            $params = $this->request->getParameters();
            $item_types = [];
            foreach ($params as $key => $val)
                if (isset($this->db_tables[$this->table][$key]['type']))
                    $item_types[] = $this->db_tables[$this->table][$key]['type'];
                else
                    throw new Exception('Item ' . $key . ' not valid');
            array_unshift($this->bind_params_types, ...$item_types);

            //  $this->bind_params_values, $this->bind_params_types
            // updateRecord(?string $table, string $conditions, array $items, array $conditions_values, array $types)
            $res = $this->updateRecord($this->table, $conditions, $params, $this->bind_params_values, $this->bind_params_types);

            if ($res)
            {
                $this->response->setStatusCode(CResponse::HTTP_STATUS_OK);
                $ret = true;
            }
            else
            {
                $this->response->setStatusCode(CResponse::HTTP_STATUS_NOT_MODIFIED);
                throw new Exception('No affected rows');
            }
        }
        catch (Exception $ex)
        {
            $this->response->setMessageError($ex->getMessage());
        }

        return $ret;
    }


    /**
     * Delete a record into a DB table
     *
     * @return bool true if the operation was successful, false in all other cases
     */
    protected function _deleteDB(): bool
    {
        $ret = false;

        $filters = $this->request->getFilters();

        if (count($filters))
        {
            $conditions = $this->getConditions();

            $res = $this->deleteRecord(null, $conditions, $this->bind_params_values, $this->bind_params_types);
            if ($res)
            {
                $this->response->setStatusCode(CResponse::HTTP_STATUS_OK);
                $ret = true;
            }
            else
            {
                $this->response->setStatusCode(CResponse::HTTP_STATUS_NOT_MODIFIED);
                throw new Exception('No affected rows');
            }
        }
        else
        {
            $this->response->setStatusCode(CResponse::HTTP_STATUS_NOT_MODIFIED);
            throw new Exception('It must be at least one condition for delete');
        }

        return $ret;
    }


    /**
     * Import one or more files to upload
     *
     * @return string list of all uploaded files separated by semicolons 
     * @throw in case of an error
     */
    protected function _inputFile(): string
    {
        $filenames = '';
        $ret = '';

        try
        {
            if (isset($_FILES) && count($_FILES) > 0)
            {
                foreach ($_FILES as $name => $file)
                {
                    $path =  UPLOAD_FILE_PATH . '/' . $name . "/";
                    // print "\n$path\n";
                    $filename = strtolower($file['name']);

                    if ($file['error'] != 0)
                    {
                        $this->response->setStatusCode(CResponse::HTTP_STATUS_BAD_REQUEST);
                        throw new Exception('File error on file ' . $filename);
                    }
                    if (!is_dir($path))
                        mkdir($path, 0774, true);

                    $path_parts = pathinfo($filename);

                    if (!in_array(strtolower($path_parts['extension']), UPLOAD_EXT_ACCEPTED))
                    {
                        $this->response->setStatusCode(CResponse::HTTP_STATUS_NOT_MODIFIED);
                        throw new Exception('The filetype is not valid (only ' . implode(', ', UPLOAD_EXT_ACCEPTED) . ')');
                    }

                    $internal_filename = time() . '_' . $filename;
                    if (move_uploaded_file($file['tmp_name'], $path . $internal_filename))
                    {
                        $this->response->setStatusCode(CResponse::HTTP_STATUS_OK);

                        $filenames .= $filename . ';';
                        $this->parameters[$name] = $internal_filename;
                    }
                    else
                    {
                        $this->response->setStatusCode(CResponse::HTTP_STATUS_INTERNAL_SERVER_ERROR); // non puo' spostare il file
                        throw new Exception('the server cannot store the file ' . $filename);
                    }
                }
            }
        }
        catch (Exception $ex)
        {
            $ret = 'ERROR';
            $this->response->setMessageError($ex->getMessage());
        }
        finally
        {
            if (isset($_FILES))
                foreach ($_FILES as $name => $file)
                    if (file_exists($file['tmp_name']))
                        unlink($file['tmp_name']);
        }
        if ($ret == 'ERROR')
            throw new Exception($this->response->getMessageError());

        return $filenames;
    }


    /**
     * Remove newly uploaded files in case of any other errors
     *
     * @return void
     */
    protected function _removeUploadedFile()
    {
        if (isset($_FILES))
            foreach ($_FILES as $name => $file)
            {
                $path =  UPLOAD_FILE_PATH . '/' . $name . "/";
                if (file_exists($path . $this->parameters[$name]))
                    unlink($path . $this->parameters[$name]);
            }
    }


    /**
     * Invalid all cache related to current table and all related
     *
     * @return void
     */
    protected function _invalidateRelatedCache()
    {
        if ($this->cache)
        {
            $this->cache->clearArea($this->table);

            // Elimina anche la cache nelle tabelle correlate
            $related_fieldname = str_replace('[related_table]', $this->table, RELATED_TABLE_ID);
            foreach ($this->db_tables as $table_rel => $fields)
                if (isset($fields[$related_fieldname]))
                    $this->cache->clearArea($table_rel);
        }
    }


    /**
     * Import all Uploaded files
     *
     * @return array message result of upload operation
     */
    public function execUUPLOAD(): array
    {
        try
        {
            $filenames = $this->_inputFile();

            if ($filenames && $this->response->getStatusCode() == CResponse::HTTP_STATUS_OK)
                $ret = ['message' => 'Operation completed successfully (uploaded: $filenames)'];
        }
        catch (Exception $ex)
        {
            $ret = ['message' => $ex->getMessage()];
        }

        return $ret;
    }


    /**
     * Get a content in cache if exists
     * 
     * @param string $key the content that uniquely identifies the cache
     *
     * @return ?array results of query or null if there is no data in the cache
     */
    protected function _getCacheKey(string $key): string
    {
        $cache_key = ''; // request new data
        if ($this->cache)
            $cache_key = ((isset($this->user)) ? ($this->user->getId() . '.') : ('')) . hash('sha256', $key);

        return $cache_key;
    }


    /**
     * Get a content in cache if exists
     *
     * @param $cache_key name of cache
     * 
     * @return ?array results of query or null if there is no data in the cache
     */
    protected function _getCachedData(string $cache_key): ?array
    {
        $data = null; // request new data
        if ($this->cache)
        {
            try
            {
                $this->cache->setArea($this->table);
                $data = $this->cache->get($cache_key);
            }
            catch (\Exception)
            {
                $data = null; // request new data
            }
        }

        return $data;
    }


    protected function _getContentFromDB(mixed $sql, string $cache_key = '') : array
    {
        $data = [];
        $data['data'] = [];

        $this->getQueryDataTable($data['data'], $sql, $this->bind_params_values, $this->bind_params_types);

        // $sql = $sc->select('COUNT(*)')->from($this->table)->where($this->getConditions())->paging(0, 0);
        $sql->select('COUNT(*)')->paging(0, 0);

        // getQueryDataValue(string $sql, string|int $name_col = '0', ?int $pos_row = null)
        $total_items = (int)$this->getQueryDataValue($sql);
        $items_per_page = $this->request->getItemsPerPage();
        $total_pages = (($items_per_page > 0 && $total_items > 0) ? (ceil($total_items / $items_per_page)) : (1));

        $data['metadata'] = [
            'total_items' => $total_items,
            'page' => $this->request->getCurrentPage(),
            'total_pages' => $total_pages,
            'items_per_page' => $items_per_page
        ];

        if ($this->cache && $cache_key && $data['data'] !== null && count($data['data']) > 0)
            $this->cache->set($cache_key, $data);

        return $data;
    }
    
    /**
     * Manager of HTTP Method GET (Read data)
     *
     * @return ?array results of query
     * @throw on error
     */
    public function execGET(): ?array
    {
        try
        {
            if ($this->db_tables !== null && !isset($this->db_tables[$this->table]))
                throw new Exception('You must set a valid table name');

            $this->response->setStatusCode(CResponse::HTTP_STATUS_OK);

            $letter_table = 'a';

            $db_composer = CSqlComposerBase::getComposerClass();
            $sql = new $db_composer();
            $sql = new \DressApi\Core\DBMS\CMySqlComposer();
            $sql->from($this->table, $letter_table++);

            if ($this->request->getWithRelations() && $this->items_view!='*')
            {
                $fields = explode(',',$this->items_view);

                if (isset($fields))
                    foreach($fields as &$field) // $field = id_area
                    {
                        $matches = [];
                        if (preg_match(RELATED_TABLE_FROM_ID,$field,$matches))
                        {
                            $rel_table = $matches[1];

                            if ($rel_table==SAME_TABLE_ID) // Reference to the same table (i.e: tree with parent)
                                $rel_table = $this->table;

                            $sql->leftJoin($rel_table,"$letter_table.id=a.$field", $letter_table);
                            if (isset($this->related_field_names[$rel_table]))
                                $field = "$letter_table.".$this->related_field_names[$rel_table]." '$rel_table'";
                            else
                                if (isset($this->related_field_names['*']))
                                    $field = "$letter_table.".$this->related_field_names['*']." '$rel_table'";

                            $letter_table++;
                        }
                        else
                            $field = "a.$field";
                    }
                $items_view = implode(',',$fields);

                $sql->select($items_view);
                $sql->where($this->getConditions());
                $sql->paging($this->request->getCurrentPage(), $this->request->getItemsPerPage());
    
                $order_by = $this->request->getOrderBy();
                if (count($order_by) > 0)
                    $sql = $sql->orderBy(str_replace('by ','by a.',$order_by));
            }
            else // simple table
            {
                $sql->select($this->items_view)->from($this->table);
                $sql->where($this->getConditions());
                $sql->paging($this->request->getCurrentPage(), $this->request->getItemsPerPage());
    
                $order_by = $this->request->getOrderBy();
                if (count($order_by) > 0)
                    $sql = $sql->orderBy($order_by);    
            }

/*
            // print "$sql\n";
$full_sql = (string)$sql;
print "\n$full_sql\n";
die;
*/
            $cache_key = $this->_getCacheKey($sql);
            $data = $this->_getCachedData($sql, $cache_key);
            if ($data===null)
                $data = $this->_getContentFromDB($sql, (string)$cache_key);     
        }
        catch (Exception $ex)
        {
            $this->response->setMessageError($ex->getMessage());
            $this->response->setStatusCode(CResponse::HTTP_STATUS_NOT_FOUND); // richiesta errata
        }
        //print_r($result);

        //        print_r($data); exit;

        $this->_revalidateHttpCache();

        return $data;
    }


    /**
     * Manager of HTTP Method HEAD (as GET but without body)
     *
     * @return risultato dell'operazione
     * @throw on error
     */
    public function execHEAD(): array
    {
        $this->execGet();
        return ['message' => 'OK'];
    }


    /**
     * Manager of HTTP Method PUT (update with full data required)
     * Update of one or more records of the table
     *
     * @return array message result of update operation
     * @throw on error
     */
    public function execPUT(): array
    {
        $res = $this->_updateDB(true); // true=tutti i parametri devono essere presenti       

        if (!$res)
            $ret = ['message' => $this->response->getMessageError() ];
        else
        {
            $ret = ['message' => 'Operation completed successfully'];

            $this->_invalidateRelatedCache();
        }

        return $ret;
    }


    /**
     * Manager of HTTP Method PUT (update without full data required)
     * Update of one or more records of the table
     *
     * @return array message result of update operation
     * @throw on error
     */
    public function execPATCH(): array
    {
        $res = $this->_updateDB(false); // true=tutti i parametri devono essere presenti       

        if (!$res)
            $ret = ['message' => $this->response->getMessageError()];
        else
        {
            $ret = ['message' => 'Operation completed successfully'];
            $this->_invalidateRelatedCache();
        }

        return $ret;
    }


    /**
     * Manager of HTTP Method POST (insert)
     * 
     * @return array message result of insert operation
     * @throw on error
     */
    protected function execPOST(): array
    {
        try
        {
            $ret_input_file = $this->_inputFile();

            $this->response->setStatusCode(CResponse::HTTP_STATUS_BAD_REQUEST);

            $affected_rows = $this->_insertDB();
            if ($affected_rows)
            {
                $this->response->setStatusCode(CResponse::HTTP_STATUS_CREATED);

                $ret = ['message' => 'Operation completed successfully'];
                $this->_invalidateRelatedCache();
            }
            else
            {
                $this->response->setStatusCode(CResponse::HTTP_STATUS_NOT_MODIFIED);
                throw new Exception('No affected rows');
            }
        }
        catch (Exception $ex)
        {
            $this->response->setMessageError($ex->getMessage());
            $ret = ['message' => $ex->getMessage()];
            $this->_removeUploadedFile();
        }

        return $ret;
    }


    /**
     * Manager of HTTP Method OPTIONS
     * 
     * @return array array with results all tables if not set one or a structure informations 
     */
    public function execOPTIONS(): array
    {
        if ($this->table == '')
            return [
                'requests' => ['HEAD', 'GET', 'PUT', 'POST', 'PATCH', 'DELETE', 'OPTIONS'],
                'tables' => array_keys($this->db_tables)
            ];
        else
            return $this->db_tables[$this->table];
    }


    /**
     * Manager of HTTP Method DELETE
     * 
     * @return array message result of delete operation
     */
    public function execDELETE(): array
    {
        $ret = '';
        try
        {
            $this->response->setStatusCode(CResponse::HTTP_STATUS_NOT_FOUND);

            $this->model->setFilters($this->request->getFilters());
            $this->model->checkValid(false); // Solo i parametri esplcitati, non sono necessari tutti

            $this->_invalidateRelatedCache();

            if ($this->_deleteDB())
            {
                $this->response->setStatusCode(CResponse::HTTP_STATUS_OK);
                $ret = ['message' => 'Operation completed successfully'];
                $this->_invalidateRelatedCache();
            }
            else
            {
                $this->response->setStatusCode(CResponse::HTTP_STATUS_NOT_MODIFIED);
                throw new Exception('No records were found');
            }
        }
        catch (Exception $ex)
        {
            $this->response->setMessageError($ex->getMessage());
            $ret = ['message' => $ex->getMessage()];
        }

        return $ret;
    }


    /**
     * Method exec
     *
     * Call the appropriate method (GET,POST,PUT,PATCH,DELETE,...) e return the results
     * 
     * @return string all data results
     */
    public function exec(): string
    {
        $result = [];

        $method = 'exec' . $this->method;

        try
        {
            if (method_exists($this, $method))
            {
                if ( $this->user===null || $this->user->checkPermission($this->table, $this->method))
                    $result = $this->{$method}();
                else
                {
                    $this->response->setStatusCode(CResponse::HTTP_STATUS_FORBIDDEN);
                    throw new Exception("Access denied: you do not have permission to perform this operation.");
                }
            }
            else
            {
                $this->response->setStatusCode(CResponse::HTTP_STATUS_NOT_FOUND);
                throw new Exception("The method " . $this->method . " not exists");
            }
        }
        catch (Exception $ex)
        {
            $this->response->setMessageError($ex->getMessage());
        }

        return $this->response->output($result);
    }


    /**
     * Method setCache
     *
     * Set a cache manager object
     * 
     */
    public function setCache(mixed $cache)
    {
        $this->cache = $cache;
    }


    /**
     * Method setUser
     *
     * Set a user manager object (CUser or childs)
     * 
     */
    public function setUser(mixed $user)
    {
        $this->user = $user;
    }
} // end class
