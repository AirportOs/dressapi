<?php
/**
 * 
 * DressAPI
 * @version 1.0
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @url https://dressapi.com
 * 
 * Class for a personalization items for response GET
 */
namespace DressApi\Modules\Page;

use Exception;

class CPageModel extends \DressApi\Modules\Base\CBaseModel
{   
            
    /**
     * Method getListItems()
     *
     * Return a list of column of current table
     *      *
     * @return array list of column names of current table
     */
    public function getListItems() : array
    {
        return ['id','id_user','name'];
    }        
 
} // end class
