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
 * 
 * User authentication and authorization 
 */

namespace DressApi\Core\Logger;

use Exception;
use DressApi\Core\Request\CRequest;
use DressApi\Core\Response\CResponse;
use DressApi\Core\User\CUser;

class CLogger
{

    public function __construct()
    {
    }


    /**
     * addLog
    *
    * Writes a log request on file logs/dress-apirequest.log
    *
    * @param string CUser $users user object
    * @param string CRequest $request request object (for input data)
    * @param string CResponse $response response object (for output data)
    *
    * @return void
    */
    public function addLog(CUser $user, CRequest $request, CResponse $response) : void
    {
        try
        {
            $path = realpath (__DIR__ . '/../../');
            $filename = $path.'/logs/dressapi-requests.log';
            $datarow = sprintf(date('Y-m-d H:i:s') . ' '."%3d %8s %20s %-20s %s\r\n",$response->getStatusCode(), 
                                $request->getMethod(),  
                                $user->getUsername(), $request->getRequest(), 
                                implode(',',$request->getParameters() ?? []) 
                              );

            file_put_contents($filename, $datarow, LOCK_EX|FILE_APPEND);
        }
        catch(Exception)
        {
            // if we use print or echo we could compromise the good result due to the log
            // where we can write the exception? :)
        }
    }

}