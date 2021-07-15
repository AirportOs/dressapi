<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Core/autoload.php'; // Autoloader dell'applicazione

use DressApi\Core\DBMS\CMySqlDB as CDB;       // In the future other DBMS as Oracle, PostgreSQL, MS SQL
use DressApi\Core\Cache\CFileCache as CCache; // An alternative is CRedisCache
use DressApi\Core\User\CUser;
use DressApi\Core\Request\CRequest;
use DressApi\Core\Response\CResponse;
use DressApi\Core\Logger\CLogger;

use DressApi\Modules\Base\CBaseController;

try
{
    // $module = CBaseController::GetModule(); // Module request
    $request = new CRequest();        // Input manager (but the validations is the CBaseModel class)
    $response = new CResponse();      // Output manager
    $cache = new CCache(DOMAIN_NAME); // Cache manager

    CDB::connect(DB_NAME, DB_USERNAME, DB_PASSWORD, DB_HOST, DB_PORT);

    $user = new CUser();
    $valid_token = $user->verify();

    if ($valid_token != 'OK')
        print $valid_token;
    else
    {
        // Create a role ('all') and accept all permissions
        $user->setUserRole(['all']); // // Add role "1" to current user
        $user->addRolePermission('all', '*', '*'); // Role=All, All modules, all permission

        $rest = new CBaseController($user, $request, $response, $cache);
        
        // sets all the related tables with an array and the method setRelatedFieldNames()
        // id_page => page:title 
        // id_[table] => [table]:name
        $rest->setRelatedFieldNames(['page'=>'title','*'=>'name']);  

        print $rest->exec();
    }

    //        CDB::disconnect();
}
catch (Exception $ex)
{
    print "\nERROR: ".$ex->getMessage()."\n\n";
    $response->error(400, $ex->getMessage()); // Bad request
}

// track request into log file by logger
(new CLogger())->addLog($user, $request, $response);
