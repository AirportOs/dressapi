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
namespace DressApi\modules\News;

use \DressApi\modules\base\CBaseModel;
use DressApi\core\request\CRequest;
use Exception;

class CNewsModel extends CBaseModel
{    
    /**
     * Return a list of column of current table
     *
     * @return array list of column names of current table
     */
    public function NOUSEgetListItems() : array
    {
        return ['id','name'];
    }


    /**
     * Return a list of column of current table for Admin users
     *
     * @return array list of column names of current table
     */
    public function NOUSEgetListItemsByAdmin() : array
    {
        return ['id','name'];
    }        


    /**
     * Change any attributes of the field on OPTIONS request.
     * This method can change the default attributes of the table fields, 
     * for example a "select" type field can become a "hidden" type field 
     *
     * @param &$columns array list of field properties
     */
    public function changeStructureTable(array &$columns) : void
    {
        // All attributes:
        // - html_type (type to view in HTML)
        // - default (default value)
        // - field (name of field)
        // - type (db type)
        // - null (is nullable)
        // - max (max length)
        // - options (for ENUM o SET)

        $columns['img']['html_type'] = 'file';
        $columns['img']['html_type_on_modify'] = 'readonly';
        $columns['title']['default'] = 'New Title';
        
        $columns['creation_date']['html_type'] = 'hidden';
        $columns['id__user']['html_type'] = 'none';
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
