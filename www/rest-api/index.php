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
    $module = CBaseController::GetModule(); // Module request
    $request = new CRequest($module); // Input manager (but the validations is the CBaseModel class)
    $response = new CResponse();      // Output manager

    CDB::connect(DB_NAME, DB_USERNAME, DB_PASSWORD, DB_HOST, DB_PORT);

    $user = new CUser($module);
    $valid_token = $user->verify();

    if ($valid_token != 'OK')
        print $valid_token;
    else
    {
        // import user permissions directly from the DB
        // if the appropriate role_controller_permission tables exist
        $user->importPermissionsByDB($module);

        // Crea un ruolo e accetta tutti i permessi
        $user->setUserRole([1]); // Imposta l'identificativo dell'utente
        $user->addRolePermission(1, $module, '*');

        $controller = CBaseController::GetModuleController();
        $rest = new $controller($user, $request, $response, new CCache(DOMAIN_NAME));

        //
        // Accept as input all tables except those listed below
        //
        // IMPORTANT: you must remove all data sensible for privacy and for security
        $rest->setExcludedControllers(['user', 'area']);

        //
        // Add the name of related table, if * considers all tables
        // NOTE: a good pratics is declare this inside the derived Controller if exists
        //
        $rest->addRelatedFieldName('name', 'preference'); // id_preference => preference:name
        $rest->addRelatedFieldName('name', '*'); // id_[TABLE] => [TABLE]:name

        // In alternative sets all the names of related tables with an array and the method setRelatedFieldNames()
        // $rest->setRelatedFieldNames(['user'=>'name','*'=>'name']);

        //
        // Input check for POST, PUT, PATCH
        // NOTE: a good pratics is declare this inside the derived Controller if exists
        //
        $rest->addItemRequired('periodicy', ['rule'=>'/[daily|weekly|monthly]/'], 'student' );
        $rest->addItemRequired('age', ['min'=>17], 'student' );
        $rest->addItemRequired('vote', ['name'=>'vote','min'=>18, 'max'=>30], 'student' );

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
    if ($response->getStatusCode() == CResponse::HTTP_STATUS_OK)
        $response->setStatusCode(400);
    print $response->output(["ERROR" => $ex->getMessage()]); // Bad request
}

// track request into DB by logger
(new CLogger())->addLog($user, $request, $response);