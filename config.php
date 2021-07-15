<?php
define('DOMAIN_NAME', 'DressApi.com');

// Output
define('DEFAULT_FORMAT_OUTPUT', 'json'); // json, xml, text

// User Token
define('SECRET_KEY', 'bGS6lzFqvvSQ8ALbOxatm7/Vk7mLQyzqaS34Q4oR1ew=');
define('TOKEN_ENC_ALGORITHM', 'HS512'); 
define('TOKEN_DURATION', '2 months'); // minutes, hours, days, months, years
define('PASSWORD_ENC_ALGORITHM', 'tiger192,4');


// Convetional id primary key name for all tables
define('ITEM_ID', 'id'); // probable alternative: '[table]_id', '[table]ID', 'id_[table]'

// if is omit "order-by/id", "order-by/id-ASC" or its abbreviation "ob/id-ASC"
define('DEFAULT_ORDER', 'DESC'); // DESC or ASC


// Personalize it, change with your codes
define('PWD_CRYPT', '@AShHK#Dfjdx45');

// Date or datetime field that can be set automatically as current time
define('CREATION_DATE', 'creation_date');

