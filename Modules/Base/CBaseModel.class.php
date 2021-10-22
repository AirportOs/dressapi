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
 * This class defines the basis of the data model. Unlike other REST libraries, 
 * the model automatically reads the data structure from the DB and stores it. 
 * In most cases, it will not be necessary to create a template for each new form but it is still possible, 
 * for example to define a restriction on the type of input or define which data to display.
 * 
 */
namespace DressApi\Modules\Base;

use Exception;

class CBaseModel
{
    // array delle tabelle in cui è possibile effettuare operazioni
    protected array  $all_tables = [];
    protected ?array $column_list = [];
    protected string $table = '';

    public const REGEX_INT = '/^[-]?[\d]+$/';
    public const REGEX_UINT = '/^[\d]+$/';
    public const REGEX_NUMBER = '/^\-?\d*\.?\d*$/';
    public const REGEX_BIT = '/^[0-1]+$/';
    public const REGEX_TIMESTAMP = '/^[\d]{10}$/'; // 1621344928
    public const REGEX_DATETIME = '/\d{4}\-\d{2}\-\d{2}\s{1}\d{2}:\d{2}:\d{2}/'; // 2021-05-18 15:38:00
    public const REGEX_TIME = '/^\d{2}:\d{2}:\d{2}$/'; // 15:38:00
    public const REGEX_DATE = '/^\d{4}\-\d{2}\-\d{2}$/'; // 2021-05-18
    public const REGEX_YEAR = '/^\d{4}$/'; // 2021

    public const REGEX_PHONE = '/^[+][0-9\s\.]/'; 
    public const REGEX_EMAIL = '/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9_-]+)/i';
    public const REGEX_URL   = '/^(http|https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,3}|www\.[^\s]+\.[^\s]{2,3})$/';

    public const REGEX_INDEXES = '/[\d,]+/';  // indici numerici separati da virgole
    
    /**
     * Method __construct
     *
     * @param $table $table current table name
     * @param array $all_tables list of all tables of current DB with column_list list
     *
     * @return void
     */
    public function __construct( string $table, array $all_tables )
    {
        $this->table = $table;

        if ($all_tables && isset($all_tables[$this->table]))
        {
            $this->setAllAvailableTables($all_tables);
            $this->column_list = $all_tables[$this->table];    
        }
        else
            if ($table=='all')
                $this->setAllAvailableTables($all_tables);

        if ($this->column_list)
            foreach($this->column_list as $field=>&$value)
            {
                if (str_replace('[table]',$this->table,ITEM_ID)===$field)
                {
                    $value['rule'] = self::REGEX_INDEXES;
                    $value['html_type'] = 'hidden';
                }
                else
                switch($value['type'])
                {
                    case 'INT':
                    case 'TINYINT':
                    case 'SMALLINT':
                    case 'MEDIUMINT':
                    case 'BIGINT':
                        $value['rule'] = self::REGEX_INT;
                        $value['html_type'] = 'number';
                        break;

                    case 'INT UNSIGNED':
                    case 'TINYINT UNSIGNED':
                    case 'SMALLINT UNSIGNED':
                    case 'MEDIUMINT UNSIGNED':
                    case 'BIGINT UNSIGNED':
                                $value['rule'] = self::REGEX_UINT;
                                $value['html_type'] = 'number';
                            break;
        
                    case 'NUMBER':
                    case 'FLOAT':
                    case 'DOUBLE':
                    case 'DECIMAL':
                    case 'DEC':
                            $value['rule'] = self::REGEX_NUMBER;
                            $value['html_type'] = 'decimal'; // number with step='0.01'
                            break;

                    case 'BIT':
                        $value['rule'] = self::REGEX_BIT;
                        $value['html_type'] = 'checkbox';
                        break;
                    
                    case 'TIMESTAMP':
                        $value['rule'] = self::REGEX_TIMESTAMP; // 1620344928
                        $value['html_type'] = 'datetime-local';
                        break;
        
                    case 'DATETIME':
                        $value['rule'] = self::REGEX_DATETIME; // 2020-05-18 15:38
                        $value['html_type'] = 'datetime-local';
                        break;

                    case 'TIME':
                        $value['rule'] = self::REGEX_TIME; // 15:38
                        $value['html_type'] = 'time';
                        break;

                    case 'DATE':
                        $value['rule'] = self::REGEX_DATE; // 2020-05-18
                        $value['html_type'] = 'date';
                        break;

                    case 'YEAR':
                        $value['rule'] = self::REGEX_YEAR; // 2020
                        $value['html_type'] = 'number';
                        break;

                    case 'ENUM':
                        $value['rule'] = '/['.$value['options'].']{1}/'; // red|green|yellow
                        $value['html_type'] = 'radio-list';
                        break;

                    case 'SET':
                        $value['rule'] = '/['.$value['options'].']+/'; // red|green|yellow
                        $value['html_type'] = 'checkbox-list';
                        break;

                    case 'TEXT':
                    case  'LOB':
                    case 'BLOB':
                    case 'CLOB':
                        $value['rule'] = ''; 
                        $value['html_type'] = 'textarea';
                        break;

                    // case 'CHAR':
                    // case 'VARCHAR':
                    // case 'VARCHAR2':
                    default: // textual type
                        $value['rule'] = ''; 
                        if (isset($value['max']) && $value['max']>60)
                                $value['html_type'] = 'textarea';
                        else
                        {
                            $value['html_type'] = 'text';

                            // SPECIAL HTML T$field(changed by name)
                            if (str_contains($field, 'color_')   || str_contains($field, '_color'))  $value['html_type'] = 'color';
                            if (str_contains($field, 'password_')|| str_contains($field, '_password'))  $value['html_type'] = 'password';
                            if (str_contains($field, 'file_')    || str_contains($field, '_file'))  $value['html_type'] = 'file';
                            if (str_contains($field, 'image_')   || str_contains($field, '_image') ||
                                str_contains($field, 'img_')     || str_contains($field, '_img')) $value['html_type'] = 'image';
                            
                            if (str_contains($field, 'phone_') || str_contains($field, '_phone') || 
                                str_contains($field, 'cellular_')|| str_contains($field, '_cellular'))  
                            {
                                $value['html_type'] = 'tel';
                                $value['rule'] = self::REGEX_PHONE;
                            }
                            if (str_contains($field, 'url_') || str_contains($field, '_url'))   
                            {
                                $value['html_type'] = 'url';
                                $value['rule'] = self::REGEX_URL;
                            }
                            if (str_contains($field, 'email_') || str_contains($field, '_email'))  
                            {
                                $value['html_type'] = 'email';
                                $value['rule'] = self::REGEX_EMAIL;
                            }
                        }
                        break;
                } // end switch
            }

//        print_r($this->column_list);
    }


    /**
     * Delete all controllers derived from DB tables
     * 
     * @param array $db_tables list of all table of current DB
     * @param array $excluded_controllers list of controllers derived from the DB to be excluded
     * 
     */
    public function setExcludedControllers(array $excluded_controllers = []): void
    {
        if (isset($excluded_controllers) && count($excluded_controllers) > 0)
            $this->all_tables = array_filter(
                $this->all_tables,
                    function ($k) use ($excluded_controllers)
                    {
                        return !in_array($k, $excluded_controllers);
                    },
                    ARRAY_FILTER_USE_KEY
            );
    }

    /**
     * Import all table/modules avaiable
     *
     * @param array $tables list of all table/modules avaiable
     */
    public function setAllAvailableTables(array $tables) : void
    {
        $this->all_tables = $tables;
    }        


    /**
     * Import all table/modules avaiable
     *
     * @return array list of all table/modules avaiable
     */
    public function getAllAvailableTables() : array
    {
        return $this->all_tables;
    }        


    /**
     * Method getListItems
     *
     * Return a list of column of current table
     *
     * @return array list of column names of current table
     */
    public function getListItems() : array
    {
        return array_keys($this->column_list);
    }        


    /**
     * Return a string contains all additional conditions for a GET/PUT/PATCH requests
     *
     * @return array list of column names of current table
     */
    public function getAdditionalConditions() : string
    {
        $conditions = '';

        return $conditions;
    }

    
    /**
     * Change any attributes of the field on OPTIONS request.
     * This method can change the default attributes of the table fields, 
     * for example a "select" type field can become a "hidden" type field 
     *
     * @param array list of field properties
     */
    public function changeFieldStructure(array &$struct) : void
    {
        // Usable from derived class
    }


    /**
     * Set all input's parameters
     *
     * @param array $filters all input's parameters
     *
     * @return void
     */
    public function setFilters(array $filters)
    {        
        foreach($this->column_list as $value)
        {
            $field_name = $value['field']; 
            if (isset($filters[$field_name]))
                $this->column_list[$field_name]['value'] = $filters[$field_name];
        }
    }


    /**
     * Method setRequiredInt
     *
     * @param string $field_name $field_name database field name
     * @param int $min minimum value accepted
     * @param int $max maximum value accepted
     *
     * @return void
     */
    public function setRequiredInt(string $field_name, int $min = null, int $max = null)
    {
        if (isset($this->column_list[$field_name]))
        {
            $this->column_list[$field_name]['rule'] = '/[0-9]+/';
            if ($min!==null) $this->column_list[$field_name]['min'] = $min;
            if ($max!==null) $this->column_list[$field_name]['max'] = $max;
        }
    }

    
    /**
     * Method setRequiredFloat
     *
     * @param string $field_name $field_name database field name
     * @param float $min minimum value accepted
     * @param float $max maximum value accepted
     *
     * @return void
     */
    public function setRequiredFloat(string $field_name, float $min = null, float $max = null)
    {
        if (isset($this->column_list[$field_name]))
        {
            // filter_var("1.33", FILTER_VALIDATE_FLOAT);
            $this->column_list[$field_name]['rule'] = '\d*(?:\.\d+)?';
            if ($min!==null) $this->column_list[$field_name]['min'] = $min;
            if ($max!==null) $this->column_list[$field_name]['max'] = $max;
        }
    }

    
    /**
     * Method setRule
     *
     * @param string $field_name $field_name database field name
     * @param string $pattern regular expression pattern
     *
     * @return void
     */
    public function setRule(string $field_name, string $pattern)
    {
        if (isset($this->column_list[$field_name]))
            $this->column_list[$field_name]['rule'] = $pattern;
        else
            throw new Exception("The field $field_name not exists");
    }
    

    
    /**
     * Method setRequired
     *
     * @param string $field_name database field name
     * @param bool $required true is required
     * @param int $min_length minimum length of value
     * @param int $max_length maximum length of value
     *
     * @throw an exception if the field_name not exists in the current table of DB
     * 
     * @return void
     */
    public function setRequired($field_name, bool $required, $min_length = null, $max_length = null)
    {                
        if (isset($this->column_list[$field_name]))
        {
            $this->column_list[$field_name]['required'] = $required;
            if ($min_length !== null)
                $this->column_list[$field_name]['min'] = $min_length;
            if ($max_length !== null)
                $this->column_list[$field_name]['max'] = $max_length;
        }
        else
            throw new Exception("The field $field_name not exists"); 
    }
    
    /**
     * Method setRequiredPattern
     *
     * @param string $field_name database field name
     * @param string $pattern regular expression pattern
     * @param string $options fixed and acceptable values separated by "|" (for example 'daily|weekly|monthly')
     *
     * @throw an exception if the field_name not exists in the current table of DB
     * 
     * @return void
     */
    public function setRequiredPattern(string $field_name, string $pattern, string $options = null)
    {        
        if (isset($this->column_list[$field_name]))
        {
            $this->column_list[$field_name]['rule'] = $pattern;
            if ($options !== null)
                $this->column_list[$field_name]['options'] = $options;
        }
        else
            throw new Exception("The field $field_name not exists");
    }


    /**
     * Provides the list of fields with all related attributes for the requested table
     * 
     * @return array the list of fields with related attributes
     */
    public function getFields() : array 
    {
        $related_table_from_id = '/^'.str_replace('[related_table]','([\S]*)',RELATED_TABLE_ID).'/';
        
        foreach($this->column_list as $name=>&$struct)
        {
            $struct['ref'] = '';
            $struct['display_name'] = ucfirst(str_replace('_',' ',$name));

            // if it is an index of an external record
            $matches = [];
            if (isset($struct['html_type']) && $struct['html_type']=='number' && preg_match($related_table_from_id, $struct['field'], $matches))
            {
                $rel_table = $matches[1];

                $table_check = $rel_table;
                if (!isset(RELATED_FIELD_NAMES[$table_check]) && isset(RELATED_FIELD_NAMES['*']))
                    $table_check = '*';

                if (isset(RELATED_FIELD_NAMES[$table_check]))
                {
                    $struct['html_type'] = ((!isset($this->all_tables[$rel_table]))?('hidden'):('select'));
                    $struct['display_name'] = ucfirst($rel_table);

                    if (is_array(RELATED_FIELD_NAMES[$table_check]))
                        $struct['ref'] = $rel_table.':'.str_replace('[table]',$rel_table,ITEM_ID).'-'.implode(',',RELATED_FIELD_NAMES[$table_check]);
                    else
                        $struct['ref'] = $rel_table.':'.str_replace('[table]',$rel_table,ITEM_ID).'-'.RELATED_FIELD_NAMES[$table_check];
                }
            }
            $this->changeFieldStructure($struct);
        }

        // searches for tables that contain the related field
        $related_item = str_replace('[related_table]',$this->table,RELATED_TABLE_ID);
        $related_tables = [];
        foreach($this->all_tables as $tab=>$items)
            if (isset($items[$related_item]) )
                $related_tables[] = $tab;
        
        return ['structure'=>$this->column_list,
                'metadata'=>['table'=>$this->table,
                             'key'=>str_replace('[table]',$related_table_from_id,ITEM_ID)],
                             'related_tables'=>$related_tables ]; 
    }


    /**
     * Check if the $table exists in the current DB
     * 
     * @param string $table name of table to check
     *
     * @return bool true if exists the table name in the current DB
     */
    public function existsTable(string $table) : bool { return (isset($this->all_tables) && isset($this->all_tables[$table])); }


    /**
     * Check if the field_name exists in the current DB table
     * 
     * @param string $field_name database field name
     *
     * @return bool true if exists the field in the current DB
     */
    public function existsField(string $field_name) : bool { return (isset($this->column_list) && isset($this->column_list[$field_name])); }


    /**
     * Returns an array containing the attributes of a given field
     * 
     * @param string $field_name field name
     *
     * @return array an array containing the attributes of a given field
     */
    public function getField(string $field_name) : array 
    { 
        return $this->column_list[$field_name] ?? []; 
    }

    
    /**
     * Check if the field_name exists in the current DB table
     * 
     * @param string $field_name field name
     * @param string $attr attribute'name of field
     *
     * @return string the value of attribute
     */
    public function getFieldAttribute(string $field_name, string $attr) : string 
    { 
        if (isset($this->column_list[$field_name]) && isset($this->column_list[$field_name][$attr]))
            return $this->column_list[$field_name][$attr];
        else 
            return '';
    }


    /**
     * Get a rule of field_name
     * 
     * @param string $field_name database field name
     *
     * @return string return a string contains rule and other filter conditions
     */
    public function getFieldRule($field_name) : string { return $this->column_list[$field_name]['rule'] ?? ''; }


    /**
     * Check if the input values are valid
     * 
     * @param bool $all_required if true check all fields of DB table according to its type
     *
     * @throw an exception if a least one field value is not valid
     * 
     * @return void
     * 
     */
    public function checkValid($all_required = true) // POST/PUT(all_required=true), PATCH(all_required=false)
    {
        $valid = 'OK';
        if (isset($this->column_list))
            foreach($this->column_list as $field)
                if ($field['field']==str_replace('[table]',$this->table,ITEM_ID) && (!isset($field['value']) || $field['value']==''))
                    continue;
                else
                    if (!$all_required || isset($field['value']))
                    {
                        if (isset($field['value']))
                        {
                            if (is_array($field['value']) && count($field['value'])>1)
                                $val = $field['value'][1];
                            else
                                $val = $field['value'];
                        }
                        if (isset($field['rule']) && isset($field['value']) && 
                            $field['rule']!='' && 
                            !preg_match($field['rule'], $val) && 
                            (!isset($field['null']) || $field['null']=='NO' || $val!='NULL'))
                            throw new Exception("The value '".str_replace('"','\"',$field['value'])."' for item '".$field['field']."' is not valid" );

                        if (isset($field['max']) && isset($field['value']) && (int)$field['max']>0 && strlen($val)>(int)$field['max'])
                            throw new Exception("The length of the ".$field['field']." field exceeds the maximum value of ".(strlen($val)-(int)$field['max'])." characters" );

                        if ( isset($field['required']) )
                        {
                            if (!isset($field['value']))
                                throw new Exception("The value of the ".$field['field']." is required" );
                            
                            if (isset($field['min']) && strlen($val)<(int)$field['min'])
                                throw new Exception("The length of the ".$field['field']." must at least ".$field['min']." characters" );
                        }
                    }
                    else
                        throw new Exception($field['field'].' column value is required in the table '.$this->table);
    }

} // end class
