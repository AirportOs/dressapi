# DressApi

DressApi is an**open source framework for create a modern REST API, for PHP 7.3+ (compatible with PHP 8.x) under Apache 2.0 license**.
It was written in 2020 and released in 2021 reusing the best concepts adopted in the AirportOs framework: a proprietary framework used for over a decade.
Its goal is to simplify the programmer's life using very few instructions but at the same time provide him with the ability to create any customization.
The name "DressApi" means it "dress up" your database, substantially it provides a quick REST API, to your db schema.
DressApi maps your database as an**ORM**(Object-relational mapping) and it does it dynamically.
Although it is structured as an**MVC**(Model, View, Controller) it does not need to define a model for each table but reads it and manages it automatically from the DB. However, you can create a Model to define some details about its data structure.
The most obvious advantage is that if the data structure changes over time, even significantly, the model fits automatically without touching a line of your code and only if you need to customize it can you create a specific model.

## Minimal but complete example code

This is a minimal example, but not a little part: it may already be all the code you will need!

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

* LAMP platform
* Composer
* Open Visual studio code project workspace and run**composer install**
* Curl for Testing (or Postman or Firefox or another if you prefer)**
* Configure your Apache configuration (add regular expression in apache.conf or your virtual host file**from _info/apache-htaccess.txt**)
* For Windows users only add text into apache.conf or your virtual host file from**_info/apache/only-windows-apache-htaccess.txt**)

## Test API with an example

* Import**file dressapi-test.sql**db into new database named "dressapi-test". The file is in**_tests/**folder.
* Set the parameters for your database: you can leave it like this or change the parameters depending on how yours will be accessible.
The parameters is in root**config.php**, be careful that it is the root config.php because there are other config.php files, in fact there is one for each module that we will see later.

```php
define('DB_HOST', 'localhost'); // server name or IP address of the server hosting the database
define('DB_PORT', 3306); // Port of DB, for mysql the default is 3306
define('DB_NAME', 'dressapi-test'); // name of DB
define('DB_USERNAME', 'root'); // Username of db user
define('DB_PASSWORD', ''); // Password of db user
```

* A few lines below, set how the database identifies users, i.e. primary key name (**id**), table name (**user**),
the name of the username and password fields. The settings are those used by the dressapi-test.sql database.

```php
define('USER_TABLE', 'user');
define('USER_ITEM_ID', 'id');
define('USER_ITEM_USERNAME', 'username');
define('USER_ITEM_PASSWORD', 'pwd');
```

* Try to run a login request as admin:
**curl -X POST [http://localhost/api/user/](http://localhost/api/user/) -d "username=admin&password=admin"**

* Copy your token, it will be your passkey for all future requests as an admin user until the token expires. It must be like this:+eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE2MjYwNDAwMjYsImp0aSI6IlJkSUx2SHdJT3FxQ3pXMkorUVdZdGc9PSIsImlzcyI6IkRyZXNzQXBpLmNvbSIsIm5iZiI6MTYyNjA0MDAyNiwiZXhwIjoxNjQxOTQxMjI2LCJkYXRhIjp7InVzZXJuYW1lIjoiYWRtaW4iLCJpZCI6MX19.CqBqDHEPWs5ZAmwew5FaOqAeQgM7XWbESEHlkceRwaPhfg_jL3xvrWPVs7hj8obEljQ9av_JJQVg29-u0s8VMw+

* Now make your request inside DB
**curl -H  "Authorization: Bearer [YOUR TOKEN]" -X GET [http://localhost/api/page/1](http://localhost/api/page/1)**

## License

DressApi is under Apache 2.0 license, you can use for free for personal and commercial projects.

## Author

DressApi was written by Tufano Pasquale

## Official site

[DressApi.com](https://dressapi.com/)
