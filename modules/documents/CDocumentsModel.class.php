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
namespace DressApi\modules\Documents;

use \DressApi\modules\base\CBaseModel;
use DressApi\core\request\CRequest;
use Exception;

class CDocumentsModel extends CBaseModel
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
     * Change items values befor insert/update into db
     *
     * @param array &$values all $values of table
     * @param string $operation type of operation ('insert' or 'modify')
     *
     * @return void
     */
    public function changeItemValues(array &$values, string $operation)
    {        
        if (isset($values['filename']))
        {
            $extension = pathinfo($values['filename'], PATHINFO_EXTENSION);
            $values['extension'] = $extension;
        } 
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

        $columns['extension']['html_type'] = 'hidden';

        $columns['filename']['html_type'] = 'file';
        $columns['filename']['html_type_on_modify'] = 'readonly'; // only if different
        
        $columns['title']['default'] = 'New Document';
    }

} // end class
