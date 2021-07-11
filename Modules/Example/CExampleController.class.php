<?php
/**
 * 
 * DressAPI
 * @version 1.0
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @url https://dressapi.com
 * 
 * 
 * Example of a Custom Controller inside a new module
 * The example shows how to join multiple tables as a response to a GET
 */

namespace DressApi\Modules\Example;

use Exception;
use DressApi\Core\Response\CResponse;
use DressApi\Modules\Base\CBaseController;

use DressApi\Core\DBMS\CSqlComposerBase;

// use DressApi\Core\Request\CRequest;
// use DressApi\Modules\Base\CModel;
// use DressApi\Core\DBMS\CDB;

class CExampleController extends CBaseController
{
    
    /**
     * Method __construct
     *
     * @return void
     */
    public function __construct( $user, $cache )
    {
        $this->setDBTable('page'); // Tabelle di default nel DB
        parent::__construct($user, $cache);
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
            // GENERIC ALTERNATIVE FOR ANY DBMS COMPOSER
            // $db_composer = CSqlComposerBase::getComposerClass();
            // $sc = new $db_composer();

            // Only for MySQL/MariaDB
            // Use this if you think you will never change DBMS. 
            // This way with a smart editor you will have more help. (if you thinever)
            $sql = new \DressApi\Core\DBMS\CMySqlComposer();


            // Make your query here...for example a LEFT JOIN
            $sql = $sql->select('*')->from('metadata','m');
            $sql->leftJoin('metadatadetail_article', 'a.id_metadata=m.id', 'a');
            $sql->where('m.id>1');
            $sql->paging($this->request->getCurrentPage(), $this->request->getItemsPerPage());

            $order_by = $this->request->getOrderBy();
            if (count($order_by) > 0)
                $sql = $sql->orderBy($order_by);

            // Alternative you can use a normal SQL CODE
            // $sql = "SELECT * FROM `metadata` m ".
            //        "LEFT JOIN `metadatadetail_article` a ON a.id_metadata=m.id ".
            //        "WHERE (m.id=1) LIMIT 0,20";

            $this->response->setStatusCode(CResponse::HTTP_STATUS_OK);

            // print "$sql\n";

            $cache_key = $this->_getCacheKey($sql);
            $data = $this->_getCachedData($sql, $cache_key);
            if ($data===null)
                $data = $this->_getContentFromDB($sql, (string)$cache_key);     
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
