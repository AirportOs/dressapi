<?php
/**
 * 
 * DressAPI
 * @version 1.0
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @copyright Tufano Pasquale
 * @url https://dressapi.com
 * 
 * 
 * User authentication and authorization 
 */

namespace DressApi\Core\Request;

use Exception;

class CRequest
{   
    protected string $sets;              // input parameters
    protected array  $params;            // values ​​to register input / update
    protected array $filters;            // filters or input/update parameters
    protected string $request;           // request table and filters
    protected bool $with_relations;      // table with names instead of indexes

    protected array $order_by = [];      // Order table by Item and type order (ASC or DESC) - i.e.: order-by/id-DESC

    protected int $page = 1;
    protected int $items_per_page = DEFAULT_ITEMS_PER_PAGE;

    protected string $http_autorization = '';

    protected static string $module;           // name of the module to display
    protected static string $table;            // name of the db table
    protected static string $method;           // method get, head, post, puth, patch, delete OR options 
    protected static string $htmlframe;        // type of form (new or modify) only for HTML response
    protected static string $format = DEFAULT_FORMAT_OUTPUT;
    protected static string $charset = DEFAULT_CHARSET;

    public function __construct()
    {
        $this->with_relations = false;
        self::$module = '';
        self::$table = '';
        self::$htmlframe = 'Read';

        $this->setHttpAuthorization();
        $this->setMethod();
        $this->setFormat();
        $this->setParameters();
        $this->setFilters();
    }


    protected function setHttpAuthorization()
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION']))
            $this->http_autorization = $_SERVER['HTTP_AUTHORIZATION'];
    }


    protected function setMethod()
    {
        self::$method = ((isset($_SERVER['REQUEST_METHOD'])) ? (strtoupper($_SERVER['REQUEST_METHOD'])) : ('GET'));
    }

    
    protected function setParameters()
    {
        // curl -d "params...."
        $this->sets = file_get_contents('php://input');

        $this->params = [];
        if ($this->sets)
        {
            try
            {
                parse_str($this->sets, $this->params);
            }
            catch (Exception)
            {
            }
        }
        
        if (isset($_POST) && $_POST)
            $this->params = array_merge($this->params, $_POST);
    }


    protected function setFilters()
    {
        // request/table/filters
        $this->request =  ((isset($_SERVER['QUERY_STRING'])) ? ($_SERVER['QUERY_STRING']) : (''));
        
        if ($this->request)
        {
            if (strpos($this->request, '/') === false) // if only one filter is the module/table
            {
                self::$table = strtolower($this->request);
                self::$module = ucfirst(self::$table);
            }
            else
            {
                $filt = explode('/', $this->request);
                
                self::$table = strtolower(array_shift($filt));
                self::$module = ucfirst(self::$table);

                if (count($filt) > 0) // second is an id ("*" for all id)
                {
                    $probably_id = $filt[0];
                    if ($probably_id == '*' || preg_match('/^[\d,]+$/', $probably_id) === 1)
                    {
                        array_shift($filt);
                        $this->filters[str_replace('[table]', self::$module, ITEM_ID)] = ['=', $probably_id];
                    }
                }
                $next_is_page = false;
                if (count($filt) > 0)
                {
                    $operators = ['<=', '>=', '<>', '=', '~', '#', '<', '>'];

                    foreach ($filt as $f)
                    {
                        if ($f == 'with-relations' || $f == 'wr')
                        {
                            $this->with_relations = true;
                            continue;
                        }
                        if ($f == 'page' || $f == 'p')
                        {
                            $next_is_page = true;
                            continue;
                        }
                        if ($next_is_page)
                        {
                            if (strpos($f, ','))
                                list($this->page, $this->items_per_page) = explode(',', $f);
                            else
                            {
                                $this->page = $f;
                                $this->items_per_page = DEFAULT_ITEMS_PER_PAGE;
                            }
                            if ($this->page < 1 && preg_match('/\d+/', $this->page) > 0)
                            {
                                $this->page = 1;
                                throw new Exception('Page error request');
                                // $this->response->output(null, CResponse::HTTP_STATUS_BAD_REQUEST);
                                break;
                            }


                            if ($this->items_per_page > MAX_ITEMS_PER_PAGE)
                                $this->items_per_page = MAX_ITEMS_PER_PAGE;

                            $next_is_page = false;
                            continue;
                        }

                        if ($f == 'order-by' || $f == 'ob')
                        {
                            $next_is_order = true;
                            continue;
                        }

                        if (!empty($next_is_order))
                        {
                            if (strpos($f, '-')) // only one order by 
                            {
                                $this->order_by = explode('-', $f);
                                if (isset($this->order_by[1]))
                                    $this->order_by[1] = strtoupper($this->order_by[1]);
                                if ($this->order_by[1] != 'ASC' && $this->order_by[1] != 'DESC')
                                    throw new Exception('The order mode is wrong: it must ASC or DESC');
                            }
                            else
                                $this->order_by = [$f, DEFAULT_ORDER];

                            $next_is_order = false;
                            continue;
                        }

                        if ($f == 'insert-form' || $f == 'modify-form')
                        {
                            self::$htmlframe = str_replace('-f','F',ucfirst($f));
                            continue;
                        }

                        if ($f == 'insert' || $f == 'modify' || $f=='delete')
                        {
                            self::$method = match ($f) 
                            {
                                'insert' => 'POST',
                                'modify' => 'PUT',
                                'delete' => 'DELETE',
                            };
                            continue;
                        }


                        // Other filters
                        $found = false;
                        foreach ($operators as $operator)
                            if (strpos($f, $operator) !== false) // must contain the operator
                            {
                                list($name, $value) = explode($operator, $f);
                                $this->filters[$name] = [$operator, $value];
                                $found = true;
                                break;
                            }
                        if (!$found) // if not found then is a name of element
                        {
                            if (isset(RELATED_FIELD_NAMES['*']))
                                $related_item = RELATED_FIELD_NAMES['*']; 
                            if (isset(RELATED_FIELD_NAMES[self::$module]))
                                $related_item = RELATED_FIELD_NAMES[self::$module];
                            if (isset($related_item))
                            {
                                if (is_array($related_item))
                                    $item_name = $related_item[0];
                                else
                                    $item_name = RELATED_FIELD_NAMES[$related_item];
                                $this->filters[$item_name] = ['#',str_replace('-','_',$f)]; // _ is a wildcard
                            }
                        }
                    }
                }
            }
        }
    }

    
    /**
     * Set format type of output
     * 
     * @param string the format type name (json, xml, html OR text) default is declared in config.php as DEFAULT_FORMAT_OUTPUT
     *               NOTE: If it is null it gets the value from HTTP_ACCEPT if it is valid otherwise it is the default value 
     */
    protected function setFormat(string $format = null): void
    {
        self::$charset = DEFAULT_CHARSET;
        if ($format == null)
        {
            $format = DEFAULT_FORMAT_OUTPUT;
            if (isset($_SERVER['HTTP_ACCEPT']))
            {
                $h = $_SERVER['HTTP_ACCEPT'];
                if (strpos($h,';')) 
                { 
                    list($h,$charset) = explode(';', $h);
                    if (strpos($charset,'charset=')) 
                        list($app,self::$charset) = explode('=', strtoupper($charset));
                }

                if (strpos($h,','))
                    $browser_accepted_list = explode(',', $h);
                else
                    $browser_accepted_list = [$h];

                foreach( $browser_accepted_list as $browser_accepted)
                {
                    list($app, $fmt) = explode('/', $browser_accepted);    
                    if (in_array($fmt,ACCEPTED_FORMAT_OUTPUT))
                    {
                        $format = $fmt;
                        break;    
                    }
                }
            }
        }

        self::$format = ( ($format == '*') ? (DEFAULT_FORMAT_OUTPUT) : (strtolower($format)) );
    }

    public static function getFormat() : string  { return self::$format; }
    public static function getCharset() : string { return self::$charset; }
    public static function getModule() : string  { return self::$module; }
    public static function getTable() : string   { return self::$table; }
    public static function getMethod() : string  { return self::$method; }
    public static function getHtmlFrame() : string    { return self::$htmlframe; }

    public function getHttpAuthorization() : string { return $this->http_autorization; }
    public function getRequest() : string      { return $this->request; }
    public function getSets() : string         { return $this->sets; }
    public function getFilters() : array      { return $this->filters ?? []; }
    public function getParameters() : array   { return $this->params ?? []; }
    public function getWithRelations() : bool  { return $this->with_relations; }
    public function getCurrentPage() : int     { return $this->page; }
    public function getItemsPerPage() : int    { return $this->items_per_page; }
    public function getFilter($name) : ?string { return $this->filters[$name] ?? null; }
    public function getParameter($name) : ?string { return $this->params[$name] ?? null; }
    public function getOrderBy() : array     { return $this->order_by ?? []; }

}