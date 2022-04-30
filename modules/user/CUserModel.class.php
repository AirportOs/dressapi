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
namespace DressApi\modules\user;

use \DressApi\modules\base\CBaseModel;
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
