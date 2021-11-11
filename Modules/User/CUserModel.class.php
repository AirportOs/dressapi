<?php
/**
 * 
 * DressAPI
 * @version 1.0
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @copyright Tufano Pasquale
 * @url https://dressapi.com
 * @year 2021
 * 
 * Class for a personalization items for response GET
 */
namespace DressApi\Modules\User;

use \DressApi\Modules\Base\CBaseModel;
use Exception;

class CUserModel extends CBaseModel
{   
            
    /**
     * Method getListItems()
     *
     * Return a list of column of current table
     *
     * @return array list of column names of current table
     */
    public function getListItems() : array
    {
        return ['id','name'];
    }


    /**
     * Method getListItemsByAdmin
     *
     * Return a list of column of current table for Admin users
     *
     * @return array list of column names of current table
     */
    public function getListItemsByAdmin() : array
    {
        return ['id','name'];
    }        

} // end class
