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

$module = CBaseController::GetModule(); // Module request
$request = new CRequest($module); // Input manager (but the validations is the CBaseModel class)
$response = new CResponse();      // Output manager

try
{
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

        $controller = CBaseController::GetModuleController();
        $rest = new $controller($user, $request, $response, new CCache(DOMAIN_NAME));

        //
        // Accept as input all tables except those listed below
        //
        // IMPORTANT: you must remove all data sensible for privacy and for security
        $rest->setExcludedControllers(['user']);

        //
        // Add the name of related table, if * considers all tables
        // NOTE: a good pratics is declare this inside the derived Controller if exists
        //
        $rest->addRelatedFieldName('name', '*'); // id_[table] => [table]:name

        print $rest->exec();
    }
}
catch (Exception $ex)
{
    print $response->error(400, $ex->getMessage());
}

// track request into DB by logger
(new CLogger())->addLog($user, $request, $response);
