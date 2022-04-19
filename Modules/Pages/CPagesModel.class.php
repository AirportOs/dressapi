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
 * Class for a personalization items for response GET
 */
namespace DressApi\Modules\Pages;

use DressApi\Modules\Base\CBaseModel;
use DressApi\Core\Cache\CFileCache as CCache;
use DressApi\Core\DBMS\CMySqlComposer as CSqlComposer;
use DressApi\Core\DBMS\CMySqlDB as CDB;
use DressApi\Core\User\CUser;
use Exception;

class CPagesModel extends CBaseModel
{   
    /**
     * @param $table $table current table name
     * @param array $all_tables list of all tables of current DB with column_list list
     * @param ?CUser $user object that manages user data
     * @param ?CCache $cache object that manages cached data
     *
     */
    public function __construct( string $table, array $all_tables, ?CUser $user = null, ?CCache $cache )
    {
        parent::__construct( $table, $all_tables, $user, $cache );
    }            

    /**
     * Return a string contains all additional conditions for a GET/PUT/PATCH requests
     *
     * @return array list of column names of current table
     */
    public function getAdditionalConditions() : string
    {
        $conditions = ""; // id_nodetype IN (SELECT id FROM nodetype WHERE name='$this->module')";

        return $conditions;
    }

    /**
     * Method getListItems()
     *
     * Return a list of column of current table
     *
     * @return array list of column names of current table
     */
    public function getListItems() : array
    {
        return ['id','title','body','description','visible','status','creation_date'];
    }        
 
  
    /**
     * Change any attributes of the field on OPTIONS request.
     * This method can change the default attributes of the table fields, 
     * for example a "select" type field can become a "hidden" type field 
     *
     * @param $columns array list of field properties
     */
    public function changeStructureTable(array &$columns) : void
    {
        if (!$this->user->isAdmin())
            $columns['id__user']['html_type'] = 'hidden';
    }


    /**
     * Change items values befor insert/update into db
     *
     * @param array &$values all $values of table
     * @param string $operation type of operation ('insert' or 'modify')
     *
     * @return void
     */
    public function changeItemValues(array &$values, string $operation)
    {   
        parent::changeItemValues($values, $operation);
    }


} // end class
