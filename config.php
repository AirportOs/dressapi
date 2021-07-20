<?php
define('DOMAIN_NAME', 'DressApi.com');

// Output
define('DEFAULT_FORMAT_OUTPUT', 'json'); // json, xml, text

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
define('RELATED_FIELD_NAMES', ['page'=>'title','*'=>'name']);


// REQUIRED ITEMS AND ACCEPTED INPUT VALUES (with rule expressions)
// Input check valid for POST, PUT, PATCH
// NOTE: you can also set required items in the controller with addItemRequired();
//       i.e: $this->addItemRequired('vote', ['name'=>'vote','min'=>18, 'max'=>30], 'student' );
define('REQUIRED_ITEMS', ['student'=> // table
                            [ // item=>rules
                             ['periodicy' => '/[daily|weekly|monthly]/'],
                             ['name'=>'age','min'=>17],
                             ['name'=>'vote','min'=>18, 'max'=>30]
                           ]
                        ]);



// if is omit "order-by/id", "order-by/id-ASC" or its abbreviation "ob/id-ASC"
define('DEFAULT_ORDER', 'DESC'); // DESC or ASC

// Please, personalize it change me if you use it! crypt/decrypt by db
define('PWD_CRYPT', '@AShHK#Dfjdx45');

// Default of total rows for each request
define('DEFAULT_ITEMS_PER_PAGE', 20);

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
define('CACHE_PATH', '/dev/shm/dressapi-cache/');

// Others 
// Preferably create a memory virtual disk
// define('CACHE_PATH', '/var/www/dressapi-cache/');
