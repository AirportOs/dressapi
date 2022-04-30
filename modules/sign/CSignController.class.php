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
 * 
 * Sign is a base Module dedicated to Login, Logout, Subscribtion and Unsubscription
 */

namespace DressApi\modules\Sign;

use Exception;
use DressApi\core\user\CUser;
use DressApi\core\cache\CFileCache as CCache;
use DressApi\core\request\CRequest;
use DressApi\core\response\CResponse;
use DressApi\modules\base\CBaseController;
use DressApi\core\dbms\CMySqlComposer as CSqlComposer;

class CSignController extends CBaseController
{
    
    /**
     * Method __construct
     *
     * @return void
     */
    public function __construct(CRequest $request, CResponse $response, ?CUser $user = null, ?CCache $cache = null)
    {
        $this->setDBTable(USER_TABLE); // Default dbtable
        parent::__construct($request, $response, $user, $cache);

/*
        $cache_attr_name = 'nodetype';
        if ($this->cache && $this->cache->exists($cache_attr_name,'structures'))
            $this->nodetypes = $this->cache->get($cache_attr_name);
        else
        {
            $sql = new CSqlComposer();
            $sql->select('id,name')->from('nodetype');

            $db = new CDB(); 
            $db->query($sql);
            $this->nodetypes = $db->getIdsArray(0, 'name');
    
            if ($this->cache)
                $this->cache->set($cache_attr_name, $this->nodetypes);    
        }
*/

    }


    /**
     * Manager of HTTP Method GET (Read data)
     *
     * @return ?array results of query
     * @throw on error
     */
    public function execGET(): ?array
    {
        $data = [];
        try
        {
            $this->response->setStatusCode(CResponse::HTTP_STATUS_OK);
        }
        catch (Exception $ex)
        {
            $this->response->setMessageError($ex->getMessage());
            $this->response->setStatusCode(CResponse::HTTP_STATUS_NOT_FOUND); // richiesta errata
        }
        //print_r($result);

        //        print_r($data); exit;

        $this->_revalidateHttpCache();

        return $data;
    }
   
} // end class
