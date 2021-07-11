<?php
define('DOMAIN_NAME', 'DressApi.com');


// DB CONFIGURATION
// parametri per la connessione al database
if (!defined('DB_HOST'))            define('DB_HOST', 'localhost');
if (!defined('DB_PORT'))            define('DB_PORT', 3306);
if (!defined('DB_NAME'))            define('DB_NAME', 'dressapi-test');
if (!defined('DB_USERNAME'))        define('DB_USERNAME', 'root');
if (!defined('DB_PASSWORD'))        define('DB_PASSWORD', '');

if (!defined('DBMS_TYPE'))            define('DBMS_TYPE', 'MySql'); // Oracle, PostgreSQL,...


if (!defined('DEBUGGING'))          define('DEBUGGING', false);
if (!defined('DEBUGGING_SELECT'))   define('DEBUGGING_SELECT', false);

if (!defined('PWD_CRYPT'))          define('PWD_CRYPT', '@AShHK#Dfjdx45');
if (!defined('AES_CRYPT_CODE'))      define('AES_CRYPT_CODE', 'F3229A0B371ED2D9441B830D21A390C3');

// Output
if (!defined('DEFAULT_FORMAT_OUTPUT')) define('DEFAULT_FORMAT_OUTPUT', 'text'); // json, xml, debug

// USER 
define('USER_ITEM_ID', 'id');
define('USER_TABLE', 'user');
define('USER_ITEM_USERNAME', 'username');
define('USER_ITEM_PASSWORD', 'pwd');

define('CREATION_DATE', 'creation_date');

// User Token
define('SECRET_KEY', 'bGS6lzFqvvSQ8ALbOxatm7/Vk7mLQyzqaS34Q4oR1ew=');
define('TOKEN_ENC_ALGORITHM', 'HS512'); 
define('TOKEN_DURATION', '6 months'); // minutes, hours, days, months, years
define('PASSWORD_ENC_ALGORITHM', 'tiger192,4');


