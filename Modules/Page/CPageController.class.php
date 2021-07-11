<?php
/**
 * 
 * Controller.class.php
 * 
 * @author Pasquale Tufano 
 * @year 2021
 * @updated: 2021-06-20
 * 
 */

namespace DressApi\Modules\Page;

use DressApi\Modules\Base\CBaseController;

class CPageController extends CBaseController
{
    
    /**
     * Method __construct
     *
     * @return void
     */
    public function __construct(  $user, $cache, $real_table = 'page' )
    {
        parent::__construct($user, $cache, $real_table);
    }
    
            
    /**
     * Method setListItems()
     *
     * Return a list of setListItems()
     *
     * @return array contenente l'elenco delle colonne della tabella
     */
    public function setListItems()
    {
        return "id,id_user,name";
    }        

       
} // end class
