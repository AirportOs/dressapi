<?php
define('DOMAIN_NAME', 'DressApi.com');


//
// DB CONFIGURATION
//

// server name or IP address of the server hosting the database
if (!defined('DB_HOST'))            define('DB_HOST', 'localhost');

// Port of DB, for mysql the default is 3306
if (!defined('DB_PORT'))            define('DB_PORT', 3306);

// name of DB
if (!defined('DB_NAME'))            define('DB_NAME', 'dressapi-test');

// Username of db user
if (!defined('DB_USERNAME'))        define('DB_USERNAME', 'root');

// Password of db user
if (!defined('DB_PASSWORD'))        define('DB_PASSWORD', '');

// For the moment you can use only MySql
if (!defined('DBMS_TYPE'))            define('DBMS_TYPE', 'MySql'); // Oracle, PostgreSQL,...


// Convetional id primary key name for all tables
define('ITEM_ID', 'id'); // probable alternative: '[table]_id', '[table]ID', 'id_[table]'

// Leave "false" in production!
if (!defined('DEBUGGING'))          define('DEBUGGING', false);
if (!defined('DEBUGGING_SELECT'))   define('DEBUGGING_SELECT', false);

// Personalize it, change with your codes
if (!defined('PWD_CRYPT'))          define('PWD_CRYPT', '@AShHK#Dfjdx45');
if (!defined('AES_CRYPT_CODE'))      define('AES_CRYPT_CODE', 'F3229A0B371ED2D9441B830D21A390C3');

// Output
if (!defined('DEFAULT_FORMAT_OUTPUT')) define('DEFAULT_FORMAT_OUTPUT', 'json'); // json, xml, debug

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

// Date or datetime field that can be set automatically as current time
define('CREATION_DATE', 'creation_date');

// User Token
define('SECRET_KEY', 'bGS6lzFqvvSQ8ALbOxatm7/Vk7mLQyzqaS34Q4oR1ew=');
define('TOKEN_ENC_ALGORITHM', 'HS512'); 
define('TOKEN_DURATION', '6 months'); // minutes, hours, days, months, years
define('PASSWORD_ENC_ALGORITHM', 'tiger192,4');


