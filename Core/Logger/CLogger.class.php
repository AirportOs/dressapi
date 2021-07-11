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
 * User authentication and authorization 
 */

namespace DressApi\Core\Logger;

use Exception;
use DressApi\Core\DBMS\CMySqlDB as CDB;
use DressApi\Core\Request\CRequest;
use DressApi\Core\Response\CResponse;
use DressApi\Core\User\CUser;

class CLogger extends CDB 
{

    public function __construct()
    {
    }


    public function addLog(CUser $user, CRequest $request, CResponse $response)
    {
        $items = [
                  'request'=>$request->getRequest(),
                  // 'request_date'=>date('Y-m-d H:i:s'),
                  'method'=>$request->getMethod(),
                  'params'=>implode(',',$request->getParameters() ?? []),
                  'status_code'=>$response->getStatusCode(),
                  'id_user'=>(($user!==null)?($user->getId()):(null))
                 ];
        $types = ['VARCHAR','VARCHAR','VARCHAR','INT','INT'];

        $this->insertRecord('logger', $items, $types);
    }

}