<?php
 declare(strict_types=1);

 session_start();

// echo "<pre>";print_r($_SERVER);exit;

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../core/autoload.php'; // Autoloader dell'applicazione

require_once __DIR__ . '/../core/generic_functions.php'; // Autoloader dell'applicazione

use DressApi\core\dbms\CSqlComposerBase;

use DressApi\core\dbms\CMySqlDB as CDB;       // In the future other DBMS as Oracle, PostgreSQL, MS SQL
use DressApi\core\cache\CFileCache as CCache; // An alternative is CRedisCache
use DressApi\core\user\CUser;
use DressApi\core\request\CRequest;
use DressApi\core\response\CResponse;
use DressApi\core\logger\CLogger;
use DressApi\modules\base\CBaseController;

try
{       
    CDB::connect(DB_NAME, DB_USERNAME, DB_PASSWORD, DB_HOST, DB_PORT);
  
/*
    CSqlComposerBase::setRenamedTables( ['acl'=>'_acl', 
                                         'config'=>'_config',
                                         'module'=>'_module',
                                         'role'=>'_role',
                                         'route'=>'_route',
                                         'user'=>'_user',
                                         'user_role'=>'_user_role',
                                        ] );
*/
    $cache = new CCache(DOMAIN_NAME,APP_NAME); // Cache manager

    $request = new CRequest();        // Input manager (but the validations is the CBaseModel class)
    $response = new CResponse();      // Output manager

//    print_r($request); print_r($response);

    $user = new CUser($request, $cache);
    $valid_token = $user->run();

    if (!$user->isAnonymous() && $valid_token != 'OK')
    {
        $response->setStatusCode(CResponse::HTTP_STATUS_OK);
        print $valid_token;
    }
    else
    {
        // 
        $cache->setUid($user->getId());


        // import user permissions directly from the DB
        // if the appropriate acl, module and role tables exist
        $imported_permission = $user->importACL($request);

        // If there are no permissions to import then it allows you to do everything
        // Create a role ('all') and accept all permissions
        // This operation is for general purposes, if you have indicated the permissions 
        // in the "acl" table then delete these instructions
        // if (!$imported_permission)
        // {
        //     $user->setUserRole(['all']); // // Add role "all" to current user
        //     $powers = ['can_read'=>'YES','can_insert'=>'YES','can_update'=>'YES','can_delete'=>'YES'];
        //     $user->addRolePermission('all', '*', $powers, false); // Role=all, All modules, all permission, only_owner  
        // }

        // Create an appropriate Controller for the request 
        // (if you use additional modules besides CBaseController, i.e.: CExampleController)
        $controller_name = CBaseController::getControllerName();
        $controller = new $controller_name($request, $response, $user, $cache);

// print_r($controller);

        //
        // It excludes the management of the tables or modules listed below.
        // Not necessary if it is managed from ACL
        // if (!$imported_permission && !$user->hasRole('Administrator'))
        //    $rest->setExcludedControllers([USER_TABLE]);
        
        if (defined('REQUIRED_ITEMS'))
            $controller->setItemsRequired(REQUIRED_ITEMS);            

        // sets all the related tables with an array and the method setRelatedFieldNames()
        if (defined('RELATED_FIELD_NAMES'))
            $controller->setRelatedFieldNames(RELATED_FIELD_NAMES);  


//        $metainfo = []; 
//        $metainfo['modules'] = $user->getAllAvaiableModules(); 

        print $controller->exec();
    }


    //        CDB::disconnect();
}
catch (Exception $ex)
{
    print $response->error(($ex->getCode())?$ex->getCode():CResponse::HTTP_STATUS_BAD_REQUEST, $ex->getMessage());
}

// track request into log file by logger
(new CLogger())->addLog($user, $request, $response);
