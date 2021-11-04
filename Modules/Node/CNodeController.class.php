<?php
/**
 * 
 * CNodeController.class.php
 * 
 * DressAPI
 * @version 1.0
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @copyright Tufano Pasquale
 * @url https://dressapi.com
 * @year 2020-2021
 * @updated: 2021-11-04
 * 
 */

namespace DressApi\Modules\Node;

use DressApi\Core\User\CUser;
use DressApi\Core\Cache\CFileCache as CCache;
use DressApi\Core\Request\CRequest;
use DressApi\Core\Response\CResponse;
use DressApi\Modules\Base\CBaseController;

class CNodeController extends CBaseController
{
    
    /**
     * Method __construct
     *
     * @return void
     */
    public function __construct(CRequest $request, CResponse $response, ?CUser $user = null, ?CCache $cache = null,
                                 $real_table = 'node')
    {
        $this->table = $real_table; // from 'example' module to real table 'node'
        parent::__construct($request, $response, $user, $cache);
    }
       
} // end class
