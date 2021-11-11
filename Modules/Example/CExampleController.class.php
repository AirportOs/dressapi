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
 * 
 * Example of a Custom Controller inside a new module
 * The example shows how to join multiple tables as a response to a GET
 */

namespace DressApi\Modules\Example;

use Exception;
use DressApi\Core\User\CUser;
use DressApi\Core\Cache\CFileCache as CCache;
use DressApi\Core\Request\CRequest;
use DressApi\Core\Response\CResponse;
use DressApi\Modules\Base\CBaseController;
use DressApi\Core\DBMS\CMySqlComposer as CSqlComposer;

class CExampleController extends CBaseController
{
    
    /**
     * Method __construct
     *
     * @return void
     */
    public function __construct(CRequest $request, CResponse $response, ?CUser $user = null, ?CCache $cache = null)
    {
        $this->setDBTable('page'); // Default dbtable
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
            $sql = new CSqlComposer();

            // Make your query here...for example a LEFT JOIN
            $sql = $sql->select('*')->from('metadata','m');
            $sql->leftJoin('metadatadetail_article', 'a.id_metadata=m.id', 'a');
            $sql->where('m.id>1');
            $sql->paging($this->request->getCurrentPage(), $this->request->getItemsPerPage());

            $order_by = $this->request->getOrderBy();
            if (count($order_by) > 0)
                $sql = $sql->orderBy($order_by);

            // Alternative you can use a normal SQL CODE 
            // NOTE: if not standard you could be a problems with other DBMS
            // $sql = "SELECT * FROM `metadata` m ".
            //        "LEFT JOIN `metadatadetail_article` a ON a.id_metadata=m.id ".
            //        "WHERE (m.id=1)";

            $this->response->setStatusCode(CResponse::HTTP_STATUS_OK);

            // print "$sql\n";

            $data = $this->_getContentFromDB($sql);     
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
