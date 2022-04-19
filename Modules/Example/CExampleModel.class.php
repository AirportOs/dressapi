<?php
/**
 * 
 * DressAPI
 * @version 1.1
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @copyright Tufano Pasquale
 * @url https://dressapi.com
 * @year 2020-2022
 * 
 * Example of a Custom Model
 */

namespace DressApi\Modules\Example;

use DressApi\Core\Cache\CFileCache as CCache;
use DressApi\Core\User\CUser;
use Exception;

class CExampleModel extends \DressApi\Modules\Base\CBaseModel
{
    public const REGEX_CF = '/^[A-Z]{6}\d{2}[ABCDEHLMPRST]{1}\d{2}[A-Z]{1}[\d]{3}[A-Z]{1}$/';

    /**
     * @param $table $table current table name
     * @param array $all_tables list of all tables of current DB with column_list list
     * @param ?CUser $user object that manages user data
     * @param ?CCache $cache object that manages cached data
     *
     */
    public function __construct( string $table, array $all_tables, ?CUser $user = null, ?CCache $cache )
    {
        parent::__construct($table, $all_tables, $user, $cache);

        // Other useful method:
        // public function getAdditionalConditions() : string
        //
        // public function setRequiredInt($item, $min = null, $max = null);
        // public function setRequiredFloat($item, $min = null, $max = null);
        // public function setRule($item, $pattern);
        // public function setRequired($item, bool $required, $min = null, $max = null);
        // public function setRequiredPattern($item, $pattern, $options = null);


        // Predefined regular expressions
        // REGEX_INT, REGEX_UINT, REGEX_NUMBER, REGEX_BIT, REGEX_TIMESTAMP,
        // REGEX_DATETIME, REGEX_TIME, REGEX_DATE, REGEX_YEAR, REGEX_PHONE, 
        // REGEX_EMAIL, REGEX_URL, REGEX_INDEXES
        
        // Italian fiscal code (with predefined regular expression as a rule )
        // setRequiredPattern(string $field_name, string $pattern, string $options = null)
        $this->setRequiredPattern('cf', self::REGEX_CF);
    }

    
    /**
     * Import all table/modules avaiable
     *
     * @param array $tables list of all table/modules avaiable
     */
    public function setAllAvailableTables(array $tables) : void
    {
        parent::setAllAvailableTables($tables);
    }        


    /**
     * Import all table/modules avaiable
     *
     * @return array list of all table/modules avaiable
     */
    public function getAllAvailableTables() : array
    {
        return parent::getAllAvailableTables();
    }        


    /**
     * Return a list of column of current table
     *
     * @return array list of column names of current table
     */
    public function getListItems() : array
    {
        // i.e.:
        // return ['id', 'id__user', 'name'];

        // default all fields of current table
        
        return parent::getListItems();
    }        

//
//    /**
//     * Return a list of column of current table for Admin users
//     *
//     * @return array list of column names of current table
//     */
//    public function getListItemsByAdmin() : array
//    {
//        // i.e.:
//        // return ['id', 'id__user', 'name'];
//
//        // default all fields of current table
//
//        return parent::getListItemsByAdmin();
//    }        
//
//
//    /**
//     * Return a string contains all additional conditions for a GET/PUT/PATCH requests
//     *
//     * @return array list of column names of current table
//     */
//    public function getAdditionalConditions() : string
//    {
//        // i.e.: 
//        return "id_nodetype IN (SELECT id FROM nodetype WHERE name='$this->module')";
//
//        return parent::getAdditionalConditions();
//    }
//
//    
//    /**
//     * Change any attributes of the field on OPTIONS request.
//     * This method can change the default attributes of the table fields, 
//     * for example a "select" type field can become a "hidden" type field 
//     *
//     * @param &$columns array list of field properties
//     * 
//     * 
//     */
//    public function changeStructureTable(array &$columns) : void
//    {
//        // Usable from derived class
//
//        // All attributes:
//        // - html_type (type to view in HTML)
//        // - default (default value)
//        // - field (name of field)
//        // - type (db type)
//        // - null (is nullable)
//        // - max (max length)
//        // - options (for ENUM o SET)
//
//        // FOR EXAMPLE:
//        // $columns['img']['html_type'] = 'file';
//        // $columns['title']['default'] = 'New Title';
//
//        parent::changeStructureTable($columns);
//    }
//
//    /**
//     * Set the auto user: if true when a table contains id__user set to id of the current user
//     * 
//     * @param bool $value the value to be set;
//     */
//    public function setAutoUser(bool $value = true)
//    {
//        parent::setAutoUser($value);
//    }
//
//
//     /**
//     * Set the auto creation date: if true when a table contains creation_date (or equivalent) set to the current date/datetime
//     * for the name of item creation_date see CREATION_DATE definition on main config.php file
//     * 
//     * @param bool $value the value to be set;
//     * @return bool $value if true (default), the auto user is run
//     */
//    public function setAutoCreationDate(bool $value = true)
//    {
//        parent::setAutoCreationDate($value);
//    }
//
//
//    /**
//     * Change items values befor insert/update into db
//     *
//     * @param array &$values all $values of table
//     * @param string $operation type of operation ('insert' or 'modify')
//     *
//     * @return void
//     */
//    public function changeItemValues(array &$values, string $operation)
//    {   
//        parent::changeItemValues($values, $operation);
//    }
//
//
//    /**
//     * Set all input's parameters before insert/update into db
//     *
//     * @param array $data all input's parameters
//     *
//     * @return void
//     */
//    public function setData(array $data)
//    {
//        parent::setData($data);
//    }
//
//    /**
//     * Set all conditions before read from db or delete into db
//     *
//     * @param ?array &$filters all input's parameters
//     *
//     * @return void
//     */
//    public function changeFilters(?array &$filters)
//    {        
//        parent::changeFilters($filters);
//    }
//
//
//    /**
//     * Check if the $table exists in the current DB
//     * 
//     * @param string $table name of table to check
//     *
//     * @return bool true if exists the table name in the current DB
//     */
//    public function existsTable(string $table) : bool 
//    { 
//        return parent::existsTable($table); 
//    }
//
//
//    /**
//     * Check if the field_name exists in the current DB table
//     * 
//     * @param string $field_name database field name
//     *
//     * @return bool true if exists the field in the current DB
//     */
//    public function existsField(string $field_name) : bool 
//    { 
//        return parent::existsField($field_name); 
//    }
//
//
//    /**
//     * Returns an array containing the attributes of a given field
//     * 
//     * @param string $field_name field name
//     * @return array an array containing the attributes of a given field
//     */
//    public function getField(string $field_name) : array 
//    { 
//        return parent::getField($field_name); 
//    }
//
//    
//    /**
//     * Check if the field_name exists in the current DB table
//     * 
//     * @param string $field_name field name
//     * @param string $attr attribute'name of field
//     *
//     * @return string the value of attribute
//     */
//    public function getFieldAttribute(string $field_name, string $attr) : string 
//    { 
//        return parent::getFieldAttribute($field_name, $attr);
//    }
//
//
//    /**
//     * Get a rule of field_name
//     * 
//     * @param string $field_name database field name
//     *
//     * @return string return a string contains rule and other filter conditions
//     */
//    public function getFieldRule($field_name) : string 
//    { 
//        return parent::getFieldRule($field_name); 
//    }
//
//
} // end class
