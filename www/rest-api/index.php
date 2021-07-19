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
        // if the appropriate moduletable_role_permission, moduletable and role tables exist
        // $user->importPermissionsByDB($request, $cache);

        // It excludes the management of the tables or modules listed below.
        $rest->setExcludedControllers(['user']);

        // For testing
        // Create a role ('all') and accept all permissions
        // $user->setUserRole(['all']); // // Add role "1" to current user
        // $user->addRolePermission('all', '*', '*'); // Role=All, All modules, all permission

        $module = CBaseController::GetModule(); // Module request
        $rest = new $module($request, $response, $user, $cache);
        
        if (defined('REQUIRED_ITEMS'))
            $rest->setItemsRequired( $required );            

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
