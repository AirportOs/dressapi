<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config.php';

require_once __DIR__ . '/../../vendor/autoload.php';
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
    $request = new CRequest();        // Input manager (but the validations is the CBaseModel class)
    $response = new CResponse();      // Output manager
    $cache = new CCache(DOMAIN_NAME,DB_NAME); // Cache manager

    CDB::connect(DB_NAME, DB_USERNAME, DB_PASSWORD, DB_HOST, DB_PORT);

    $user = new CUser($request);
    $valid_token = $user->verify();

    if ($valid_token != 'OK')
    {
        $response->setStatusCode(CResponse::HTTP_STATUS_OK);
        print $valid_token;
    }
    else
    {
        // import user permissions directly from the DB
        // if the appropriate acl, moduletable and role tables exist
        $imported_permission = $user->importPermissionsByDB($request, $cache);

        // If there are no permissions to import then it allows you to do everything
        // Create a role ('all') and accept all permissions
        // This operation is for general purposes, if you have indicated the permissions 
        // in the "acl" table then delete these instructions
        if (!$imported_permission)
        {
            $user->setUserRole(['all']); // // Add role "all" to current user
            $user->addRolePermission('all', '*', '*'); // Role=all, All modules, all permission
        }

        // Create an appropriate Controller for the request 
        // (if you use additional modules besides CBaseController, i.e.: CExampleController)
        $controller = CBaseController::GetModuleController();
        $rest = new $controller($request, $response, $user, $cache);

        //
        // It excludes the management of the tables or modules listed below.
        //
        // IMPORTANT: you must remove all data sensible for privacy and security
        $rest->setExcludedControllers(['user']);
        
        if (defined('REQUIRED_ITEMS'))
            $rest->setItemsRequired(REQUIRED_ITEMS);            

        // sets all the related tables with an array and the method setRelatedFieldNames()
        if (defined('RELATED_FIELD_NAMES'))
            $rest->setRelatedFieldNames(RELATED_FIELD_NAMES);  

        print $rest->exec();
    }

    //        CDB::disconnect();
}
catch (Exception $ex)
{
    print "\n".$response->error(($ex->getCode())?$ex->getCode():CResponse::HTTP_STATUS_BAD_REQUEST, $ex->getMessage())."\n";
}

// track request into log file by logger
(new CLogger())->addLog($user, $request, $response);
