# DressApi

DressApi is an <b>open source framework for create a modern REST API. for PHP 7.3+ (compatible with PHP 8.x) under Apache 2.0 license</b>.
The name "DressApi" means it "dress up" your database, substantially it provides a quick REST API, to your db schema.
DressApi maps your database as an <b>ORM</b> (Object-relational mapping) and it does it dynamically.<br>
Although it is structured as an <b>MVC</b> (Model, View, Controller) it does not need to define a model for each table but reads it and manages it automatically from the DB. However, you can create a Model to define some details about its data structure. 
    The most obvious advantage is that if the data structure changes over time, even significantly, the model fits automatically without touching a line of your code and only if you need to customize it can you create a specific model.

## Minimal but complete example code

```php
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

        // Accept as input all tables except those listed below
        $rest->setExcludedControllers(['user']);

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
    print $response->error(400, $ex->getMessage()); // Bad request
}

// track request into DB by logger
(new CLogger())->addLog($user, $request, $response);

```

## Prerequisites

<ul>
<li>LAMP platform</li>
<li>Composer</li>
<li>Open Visual studio code project workspace and run <b>composer install</b></li>
<li>Curl for Testing (or Postman or Firefox or another if you prefer)</b></li>
<li>Configure your Apache configuration (add regular expression in apache.conf or your virtual host file <b>from _info/apache-htaccess.txt</b>)</li>
<li>For Windows users only add text into apache.conf or your virtual host file from <b>_info/apache/only-windows-apache-htaccess.txt</b>)</li>
</ul>

## Test API with an example

<ul>
<li>Import <b>file dressapi-test.sql</b> db into new database named "dressapi-test". The file is in <b>_tests/</b> folder.</li>
<li>Set the parameters for your database: you can leave it like this or change the parameters depending on how yours will be accessible.
The parameters is in root <b>config.php</b>, be careful that it is the root config.php because there are other config.php files, in fact there is one for each module that we will see later.
<ul>
<li><b>define('DB_HOST', 'localhost');</b> // server name or IP address of the server hosting the database</li>  
<li><b>define('DB_PORT', 3306);</b>// Port of DB, for mysql the default is 3306</li>
<li><b>define('DB_NAME', 'dressapi-test');</b>// name of DB</li>
<li><b>define('DB_USERNAME', 'root');</b>// Username of db user</li>
<li><b>define('DB_PASSWORD', '');</b>// Password of db user</li>
</ul>
</li>
<li>A few lines below, set how the database identifies users, i.e. primary key name (<b> id </b>), table name (<b> user </b>),
the name of the username and password fields. The settings are those used by the dressapi-test.sql database.
<ul>
<li><b>define('USER_ITEM_ID', 'id');</b></li>  
<li><b>define('USER_TABLE', 'user');</b></li>
<li><b>define('USER_ITEM_USERNAME', 'username');</b></li>
<li><b>define('USER_ITEM_PASSWORD', 'pwd');</b></li>
</ul>
</li>
<li>Try to run a login request as admin:<br>
<b>curl -X POST http://dressapi/api/user/ -d "username=admin&password=admin"</b>
</li>
<li>Copy your token, it will be your passkey for all future requests as an admin user until the token expires. It must be like this:<br><i>eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE2MjYwNDAwMjYsImp0aSI6IlJkSUx2SHdJT3FxQ3pXMkorUVdZdGc9PSIsImlzcyI6IkRyZXNzQXBpLmNvbSIsIm5iZiI6MTYyNjA0MDAyNiwiZXhwIjoxNjQxOTQxMjI2LCJkYXRhIjp7InVzZXJuYW1lIjoiYWRtaW4iLCJpZCI6MX19.CqBqDHEPWs5ZAmwew5FaOqAeQgM7XWbESEHlkceRwaPhfg_jL3xvrWPVs7hj8obEljQ9av_JJQVg29-u0s8VMw</i>
</li>
<li>Now make your request inside DB<br>
<b>curl -H  "Authorization: Bearer [YOUR TOKEN]" -X GET http://dressapi/api/page/1</b>
</li>
</ul>

## License

DressApi is under Apache 2.0 license, you can use for free for personal and commercial projects. 

## Author
   
DressApi was written by Tufano Pasquale
    