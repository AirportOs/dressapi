<?php
/**
 * 
 * DressAPI
 * @version 2.0 alpha
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @copyright Tufano Pasquale
 * @url https://dressapi.com
 * @year 2020-2022
 * 
 * Class for a personalization items for response GET
 */
namespace DressApi\modules\Pages;

use DressApi\modules\base\CBaseModel;
use DressApi\core\cache\CFileCache as CCache;
use DressApi\core\dbms\CMySqlComposer as CSqlComposer;
use DressApi\core\dbms\CMySqlDB as CDB;
use DressApi\core\user\CUser;
use Exception;

class CPagesModel extends CBaseModel
{   
    /**
     * @param string $table current table name
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
     * @return string all additional conditions
     */
    public function getAdditionalConditions() : string
    {
        $user_conditions = 'a.id__user='.$this->user->getId().'';
        if ($this->user->isAnonymous())
            $status_conditions = 'status=\'public\'';
        else
            $status_conditions = 'a.status IN (\'reserved\',\'public\')';
        $conditions = "($user_conditions OR $status_conditions)"; // id_cmsnodetype IN (SELECT id FROM cmsnodetype WHERE name='$this->module')";

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
     * Change items values before insert/update into db
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
