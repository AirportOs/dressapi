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

use DressApi\Core\User\CUser;
use DressApi\Core\Request\CRequest;
use DressApi\Core\Response\CResponse;
use DressApi\Modules\Base\CBaseController;

class CPageController extends CBaseController
{
    
    /**
     * Method __construct
     *
     * @return void
     */
    public function __construct(CUser $user = null, CRequest $request, CResponse $response, $cache = null, $real_table = 'page')
    {
        $this->table = $real_table;
        parent::__construct($user, $request, $response, $cache, $real_table);
    }
       
} // end class
