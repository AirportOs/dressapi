<?php

//
// DB CONFIGURATION
//

// server name or IP address of the server hosting the database
define('DB_HOST', 'localhost');

// Port of DB, for mysql the default is 3306
define('DB_PORT', 3306);

// name of DB
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
define('USER_TABLE', 'user');

// Name of user id in your database
define('USER_ITEM_ID', 'id');

// Username name of user table in your database
define('USER_ITEM_USERNAME', 'username');

// Password name of user table in your database
define('USER_ITEM_PASSWORD', 'pwd');


define('DEBUG_LEVEL_MIN', 'info');

define('AES_CRYPT_CODE', 'F3229A0B371ED2D9441B830D21A390C3'); // Please, change me if you use it!
