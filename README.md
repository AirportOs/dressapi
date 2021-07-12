# DressApi

DressApi is an **open source framework for create a modern REST API, for PHP 7.3+ (compatible with PHP 8.x) under Apache 2.0 license**.
It was written in 2020 and released in 2021 reusing the best concepts adopted in the AirportOs framework: a proprietary framework used for over a decade.
Its goal is to simplify the programmer's life using very few instructions but at the same time provide him with the ability to create any customization.
The name "DressApi" means it "dress up" your database, substantially it provides a quick REST API, to your db schema.
DressApi maps your database as an **ORM** (Object-relational mapping) and it does it dynamically.
Although it is structured as an **MVC** (Model, View, Controller) it does not need to define a model for each table but reads it and manages it automatically from the DB. However, you can create a Model to define some details about its data structure.
The most obvious advantage is that if the data structure changes over time, even significantly, the model fits automatically without touching a line of your code and only if you need to customize it can you create a specific model.

## Minimal but complete example code

This is a minimal example but not a little part: it may already be all the code you will need!

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
* Open Visual studio code project workspace and run **composer install**
* Curl for Testing (or Postman or Firefox or another if you prefer)**
* Configure your Apache configuration (add regular expression in apache.conf or your virtual host file **from _info/apache-htaccess.txt**)
* For Windows users only add text into apache.conf or your virtual host file from **_info/apache/only-windows-apache-htaccess.txt**)

## Test API with an example

* Import **file dressapi-test.sql** db into new database named "dressapi-test". The file is in **_tests/** folder.
* Set the parameters for your database: you can leave it like this or change the parameters depending on how yours will be accessible.
The parameters is in root **config.php**, be careful that it is the root config.php because there are other config.php files, in fact there is one for each module that we will see later.

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

* Copy your token, it will be your passkey for all future requests as an admin user until the token expires. It must be like this: **eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE2MjYwNDAwMjYsImp0aSI6IlJkSUx2SHdJT3FxQ3pXMkorUVdZdGc9PSIsImlzcyI6IkRyZXNzQXBpLmNvbSIsIm5iZiI6MTYyNjA0MDAyNiwiZXhwIjoxNjQxOTQxMjI2LCJkYXRhIjp7InVzZXJuYW1lIjoiYWRtaW4iLCJpZCI6MX19.CqBqDHEPWs5ZAmwew5FaOqAeQgM7XWbESEHlkceRwaPhfg_jL3xvrWPVs7hj8obEljQ9av_JJQVg29-u0s8VMw**

* Now make your request inside DB
**curl -H  "Authorization: Bearer [YOUR TOKEN]" -X GET [http://localhost/api/page/1](http://localhost/api/page/1)**
Which requires the **id=1** of the **page** table

## Test DressApi with curl

As just seen above, each request that will be made after login is required to indicate the token through the parameter:

```bash
-H  "Authorization: Bearer [YOUR TOKEN]"
```

The instruction **- X GET** followed by a **uri** indicates the method by which the request is made.
The methods that can be used in the model defined for the REST API indicate the action to follow:

* GET **Read** request
* POST **Insertion** request
* PUT **Updating** request to update one or more records by defining all the data of the record
* PATCH **Updating** request to update one or more records, even just some data of the record
* DELETE **Deleting** request to delete one or more records
* OPTIONS **Options** to know the data structure and methods accepted by the API

The operations you can carry out with DressApi are the classic CRUD (Create, Read, Update, Delete) plus the request for OPTIONS. Among the requests there is also HEAD which is still a read operation.

Here are some examples of possible operations:

```bash
#CREATE/INSERT
curl -H  "Authorization: Bearer [YOUR TOKEN]" -X POST http://dressapi/api/comment -d "comment=Wonderful!&creation_date=2022-01-13&id_page=1"
curl -H  "Authorization: Bearer [YOUR TOKEN]" -X POST http://dressapi/api/comment -d "comment=Bleah! Lumen is better than DressApi!&id_page=1"

#UPDATE/MODIFY
curl -H  "Authorization: Bearer [YOUR TOKEN]" -X PATCH http://dressapi/api/page/1 -d "creation_date=2021-01-13"

#DELETE
curl -H  "Authorization: Bearer [YOUR TOKEN]" -X DELETE http://dressapi/api/logger/3

#READ
curl -H  "Authorization: Bearer [YOUR TOKEN]" -X GET http://dressapi/api/page/1
curl -H  "Authorization: Bearer [YOUR TOKEN]" -X GET http://dressapi/api/page/id_user=102/
```

Especially in queries, i.e. in reading, it is possible to do much more thanks to some options such as:

* The pagination
* The sorting
* The flag of the relations between two fields of two different tables of the db
* The filters

### The pagination

You can set the number of page and the total elements per page using /p or /page option.
For example:

```bash
-X GET http://dressapi/api/logger/wr/p/2 # Page 2
-X GET http://dressapi/api/logger/wr/p/2,10 # Page 2, 10 rows per page
-X GET http://dressapi/api/page/wr/page/1,10 # For module/table "page" the first page with 10 rows per page
```

### The sorting

You can determine the order in which the request records should be submitted using /ob or /order-by option followed by the name of the field to be ordered and optionally by the type of order (DESC or ASC):

```bash
-X GET http://dressapi/api/logger/ob/id
-X GET http://dressapi/api/logger/ob/id-ASC
-X GET http://dressapi/api/logger/ob/id-DESC/wr/p/1,10
```

Each option is independent from the other so it is possible to combine it with the layout and filters:

```bash
-X GET http://dressapi/api/logger/ob/id-ASC/wr
```

### The flag of the relations between two fields of two different tables of the db

You can set the number of page and the total elements per page using /wr or /with-relations option.
For example:

```bash
-X GET http://dressapi/api/logger/wr
-X GET http://dressapi/api/logger/with-relations
```

If everything is set correctly the result will be for example that "id_user" will be replaced by the equivalent "username", "email" or "name" in the user table.

### The filters

The filters extrapolate the data obtaining only those that satisfy certain conditions, the operators that can be used are:
=, &lt;, &gt;, &gt;=, &lt;=, ~

The **~** filter indicates that the searched text must be contained (and therefore not only the same) within the field.
Some examples:

```bash
-X GET http://dressapi/api/page/id_user=102/
-X GET http://dressapi/api/page/name~Welcome
-X GET http://dressapi/api/logger/wr/id_user>=100/p/1,10
```

Filters can be combined with each other by appending the condition to the uri.

## Output formats

By default the output is the JSON format but, alternatively, you can explicitly indicate other formats, at the moment there are 3: JSON, XML or TEXT

To indicate one, add one of these options:

```bash
-H 'Content-Type: application/json'
-H 'Content-Type: plain/text'
-H 'Content-Type: application/xml
```

## License

DressApi is under Apache 2.0 license, you can use for free for personal and commercial projects.

## Author

DressApi was written by Tufano Pasquale

## Official site

[DressApi.com](https://dressapi.com/)
