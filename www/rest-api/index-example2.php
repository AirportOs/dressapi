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
    $cache = new CCache(DOMAIN_NAME, DB_NAME); // Cache manager

    CDB::connect(DB_NAME, DB_USERNAME, DB_PASSWORD, DB_HOST, DB_PORT);

    $user = new CUser($request);
    $valid_token = $user->verify();

    if ($valid_token != 'OK')
        print $valid_token;
    else
    {
        // import user permissions directly from the DB
        // if the appropriate moduletable_role_permission, moduletable and role tables exist
        $user->importPermissionsByDB($request, $cache);

        // Create a role
        // $user->setUserRole([1]); // // Add role "1" to current user
        
        // Accept all permissions for the created role
        // $user->addRolePermission(1, '*', '*'); // All modules, all permission

        // Create an appropriate Controller for the request 
        // (if you use additional modules besides CBaseController, i.e.: CExampleController)
        $controller = CBaseController::GetModuleController();
        $rest = new $controller($user, $request, $response, $cache);

        //
        // It excludes the management of the tables or modules listed below.
        //
        // IMPORTANT: you must remove all data sensible for privacy and for security
        $rest->setExcludedControllers(['user']);

        //
        // Add the name of related table, if * considers all tables
        // NOTE: a good pratics is declare this inside the derived Controller if exists
        //
        $rest->addRelatedFieldName('title', 'page'); // id_preference => preference:name
        $rest->addRelatedFieldName('name', '*'); // id_[TABLE] => [TABLE]:name

        // In alternative sets all the names of related tables with an array and the method setRelatedFieldNames()
        // sets all the related tables with an array and the method setRelatedFieldNames()
        // id_page => page:title 
        // id_[table] => [table]:name
        // $rest->setRelatedFieldNames(['page'=>'title','*'=>'name']);  

        //
        // Input check for POST, PUT, PATCH
        // NOTE: a good pratics is declare this inside the derived Controller if exists
        //
        // $rest->addItemRequired('periodicy', ['rule'=>'/[daily|weekly|monthly]/'], 'student' );
        // $rest->addItemRequired('age', ['min'=>17], 'student' );
        // $rest->addItemRequired('vote', ['name'=>'vote','min'=>18, 'max'=>30], 'student' );

        // You can use addItemRequired() for each single condition
        // or a single array containing all the conditions
        // for validity of the input with method setItemsRequired()
        // $require[TABLE_NAME] = [ name and other options ]

        // $required = [];
        // $required['student'] = [
        //                         ['periodicy' => '/[daily|weekly|monthly]/'],
        //                         ['name'=>'age','min'=>17],
        //                         ['name'=>'vote','min'=>18, 'max'=>30]
        //                     ];
        // // Input check for POST, PUT, PATCH
        // $rest->setItemsRequired( $required );            

        print $rest->exec();
    }

    //        CDB::disconnect();
}
catch (Exception $ex)
{
    $response->error(400, $ex->getMessage()); // Bad request
}

// track request into DB by logger
(new CLogger())->addLog($user, $request, $response);