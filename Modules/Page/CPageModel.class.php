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
        global $user;
        if (!$user->isAdmin())
            $columns['id_user']['html_type'] = 'hidden';
    }


    /**
     * Change items values
     *
     * @param array $values all $values of table
     *
     * @return void
     */
    protected function changeItemValues(array &$values)
    {   
        // i.e.: Change only if is a Administrator
        // this operation is not required because you can use CBaseController::setAutoUser
        // global $user;
        // if (!$user->isAdmin())
        //    $value['id_user'] = $user->id;
    }


} // end class
