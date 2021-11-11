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
 * @updated: 2021-11-07
 * 
 */

namespace DressApi\Modules\News;

use DressApi\Core\User\CUser;
use DressApi\Core\Cache\CFileCache as CCache;
use DressApi\Core\Request\CRequest;
use DressApi\Core\Response\CResponse;
use DressApi\Modules\Base\CBaseController;

class CNewsController extends CBaseController
{
    
    /**
     * Constructor
     *
     * @param CRequest  $request object that contains the current request, that is, all the input data
     * @param CResponse $response object that will contain the response processed by the current object
     * @param CUser     $user object containing user information such as id, permissions, name and modules that can manage
     * @param CCache    $cache object that manages cached data
     *
     * @return void
     */
    public function __construct(CRequest $request, CResponse $response, ?CUser $user = null, ?CCache $cache = null)
    {
        parent::__construct($request, $response, $user, $cache);
    }
       
} // end class
