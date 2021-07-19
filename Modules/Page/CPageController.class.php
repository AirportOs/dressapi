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
use DressApi\Core\Cache\CFileCache as CCache;
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
    public function __construct(CRequest $request, CResponse $response, ?CUser $user = null, ?CCache $cache = null,
                                 $real_table = 'page')
    {
        $this->table = $real_table; // from 'example' module to real table 'page'
        parent::__construct($request, $response, $user, $cache);
    }
       
} // end class
