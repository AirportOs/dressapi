# DressApi

DressApi is a fast, powerful, consistent, scalable and very easy to use **open source framework for create a modern REST API, for PHP 8.x under Apache 2.0 license**.
It was written in 2020 and released in 2021 reusing the best concepts adopted in the AirportOs framework: a proprietary framework used for over a decade.
Its goal is to simplify the programmer's life by limiting himself to configuring only some parameters in the configuration file while all the data will be taken from the database and possibly from an ACL (Access control list) system present as an example in the API.
It is then possible to customize any aspect of the API by creating customized modules for every type of need.

The name "DressApi" means it "dress up" your database, substantially it provides a quick REST API, to your db schema.
DressApi maps your database as an **ORM** (Object-relational mapping) and it does it dynamically.
Although it is structured as an **MVC** (Model, View, Controller) but it does not need to define a model for each table but reads it and manages it automatically from the DB. However, you can create a Model to define some details about its data structure.
The View is only a data output in JSON, XML or TEXT format, than genarally you don't need to customize.
The most obvious advantage is that if the data structure changes over time, even significantly, the model fits automatically without touching a line of your code and only if you need to customize it can you create a specific model.

## Why use DressApi?

Surely the possibility of not writing code can be an excellent reason to choose DressApi but it is not the only peculiarity that makes it different from other frameworks. The special features of DressApi can be summarized below:

* It is native to **PHP 8**, so it uses the latest PHP features such as type declaration.

* It automatically **manages the relationships between the tables**, in most cases there is no need to make complicated queries with joins in the selections to get more meaningful names instead of indexes.

* It includes a file-based or Redis **Cache** that can automatically update itself when it is needed, for example even when a related table's values ​​change.

* It uses an **advanced query system** that includes for example the major and minor operators and also the special operator ~ which indicates that a text must be included in a certain field.

* It already contains a default **paging system**

* A **deferred connection system** allows you to connect to the DB only if necessary. This mode saves a lot of time especially when used in conjunction with the caching system: in this way the framework responds very quickly with the minimum of resources required.

* Include **JWT (JSON Web Token) for the authetication**. JWT is a proposed Internet standard for creating data encrypted by a public/private key

* **Strict input control**. The types of the DB fields are known and therefore can be checked before making an insertion or modification without adding a line of code. However, it is possible to associate a regular expression for each single field of the record to be inserted/modified.

* Especially for new databases **DressApi provides a complete system of ACL (Access Control List)** to define users, roles and possible CRUD operations for each role and eg each table.

* It is structured to be **independent of the database**, not only for the drivers/library but also in the use of the SQL dialect. At the moment only MySQL has been implemented but it is expected in the future the use of other databases such as Oracle, PostgreSQL and MS SQL for which the equivalent library has yet to be written.

* It is **highly customizable**. It is possible to create customized modules and, for more complex cases, it is possible to completely abstract from the basic logic while using various facilities such as high-level DB management, cache, JWT, ACL and more.

* **It contains an API usage example (Admin)** that can be used as a starting point or as an example to study. It includes a database to import and a couple of instructions to add to the Apache configuration.

## Configure your First App and go

This is the configuration file to set up to get a complete API from your DB! This is all you will probably need to do!

```php
<?php
define('DOMAIN_NAME', 'DressApi.com');

// Output
define('DEFAULT_FORMAT_OUTPUT', 'json'); // json, xml, plain (text/csv), debug


// Level of log info
define('LOG_LEVEL_MIN', 'info');


//
// Relations
//

// Convetional id primary key name for all tables
define('ITEM_ID', 'id'); // probable alternative: '[table]_id', '[table]ID', 'id_[table]'

// Convetional related field id naming
// to convert the indexes of related tables 
// with a more meaningful value, such as a name
define('RELATED_TABLE_ID', 'id_[related_table]');

// Index to the same table i.e: "id_parent"
// If the field name contains the same table is not necessary SAME_TABLE_ID
// i.e: if the table is "page" and one field in the same table is id_page, then the SAME_TABLE_ID is not used 
define('SAME_TABLE_ID', 'parent'); 

// Date or datetime field that can be set automatically as current time
define('CREATION_DATE', 'creation_date');


//
// RELATION OF FOREIGN KEY WITH OTHER TABLE
//

// id_page => page:title 
// id_[table] => [table]:name
// NOTE: you can also set relationships in the controller with addRelatedFieldName()
define('RELATED_FIELD_NAMES', ['page'=>['title','creation_date'], // the value can be an array of strings
                               'contact'=>['email','city'],
                               '*'=>'name']);                     // or a simple string


// REQUIRED ITEMS AND ACCEPTED INPUT VALUES (with rule expressions)
// Input check valid for POST, PUT, PATCH
// NOTE: you can also set required items in the controller with addItemRequired();
//       i.e: $this->addItemRequired('vote', ['name'=>'vote','min'=>18, 'max'=>30], 'student' );
define('REQUIRED_ITEMS', ['student'=> // table
                            [ // item=>rules
                             ['periodicy' => '/[daily|weekly|monthly]/'],
                             ['name'=>'age','min'=>17],
                             ['name'=>'vote','min'=>18, 'max'=>30],
                             
                           ]
                        ]);



// if is omit "order-by/id", "order-by/id-ASC" or its abbreviation "ob/id-ASC"
define('DEFAULT_ORDER', 'DESC'); // DESC or ASC

// Please, personalize it change me if you use it! crypt/decrypt by db
define('PWD_CRYPT', '@AShHK#Dfjdx45');

// Default of total rows for each request
define('DEFAULT_ITEMS_PER_PAGE', 2);

// Max total rows for each request, if the value is greater it will be reset to MAX_ITEMS_PER_PAGE value
define('MAX_ITEMS_PER_PAGE', 200);



//
// DB CONFIGURATION
//

// server name or IP address of the server hosting the database
define('DB_HOST', 'localhost');

// Port of DB, for mysql the default is 3306
define('DB_PORT', 3306);

// name of DB
// define('DB_NAME', 'dressapi-test');
define('DB_NAME', 'dressapi-test');

// Username of db user
define('DB_USERNAME', 'root');

// Password of db user
define('DB_PASSWORD', '');

// For the moment you can use only MySql
define('DBMS_TYPE', 'MySql'); // Oracle, PostgreSQL,...


//
// USER DB
//

// Name of user table in your database
// NOTE: if Empty string not use the user table 
//       and the authentication is not necessary!
define('USER_TABLE', 'user');

// Name of user id in your database
define('USER_ITEM_ID', 'id');

// Username name of user table in your database
define('USER_ITEM_USERNAME', 'username');

// Password name of user table in your database
define('USER_ITEM_PASSWORD', 'pwd');

// User Token
define('SECRET_KEY', 'bGS6lzFqvvSQ8ALbOxatm7/Vk7mLQyzqaS34Q4oR1ew=');
define('TOKEN_ENC_ALGORITHM', 'HS512'); 
define('TOKEN_DURATION', '2 months'); // minutes, hours, days, months, years
define('PASSWORD_ENC_ALGORITHM', 'tiger192,4');


//
// CACHE ON FILE
//

// On Debian
// Preferably create a memory virtual disk
// Not used if you use Redis as cache
define('CACHE_PATH', '/dev/shm/');

```

## Prerequisites

* LAMP platform with **PHP 8.0** or more recent
* Composer (go to directory of dressapi and run **composer install**)
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

```bash
curl -X POST http://localhost/api/user/ -d "username=admin&password=admin"
```

* Copy your token, it will be your passkey for all future requests as an admin user until the token expires. It must be like this: **eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE2MjYwNDAwMjYsImp0aSI6IlJkSUx2SHdJT3FxQ3pXMkorUVdZdGc9PSIsImlzcyI6IkRyZXNzQXBpLmNvbSIsIm5iZiI6MTYyNjA0MDAyNiwiZXhwIjoxNjQxOTQxMjI2LCJkYXRhIjp7InVzZXJuYW1lIjoiYWRtaW4iLCJpZCI6MX19.CqBqDHEPWs5ZAmwew5FaOqAeQgM7XWbESEHlkceRwaPhfg_jL3xvrWPVs7hj8obEljQ9av_JJQVg29-u0s8VMw**

* Now make your request inside DB

```bash
curl -H  "Authorization: Bearer [YOUR TOKEN]" -X GET [http://localhost/api/page/1](http://localhost/api/page/1)
```

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
-H 'Accept: application/json'
-H 'Accept: text/plain'
-H 'Accept: application/xml'
-H 'Accept: application/debug'
```

## Other features

* **DressApi use a cache** based on file (normally on virtual disk on memory) or on Redis and is integrated to the application.
This means it deletes interested parties (**including relations**) when there is an update.

* **Deferred connection of the DB**: if the request is already in cache it does not make any connection to the database.
In this way it saves unnecessary resources and is more efficient.

## License

DressApi is under Apache 2.0 license, you can use for free for personal and commercial projects.

## Author

DressApi was written by **Tufano Pasquale**

## Official site

[DressApi.com](https://dressapi.com/)
