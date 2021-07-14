<?php
define('DOMAIN_NAME', 'DressApi.com');



// Convetional id primary key name for all tables
define('ITEM_ID', 'id'); // probable alternative: '[table]_id', '[table]ID', 'id_[table]'

// Personalize it, change with your codes
define('PWD_CRYPT', '@AShHK#Dfjdx45');

// Output
define('DEFAULT_FORMAT_OUTPUT', 'json'); // json, xml, debug

// Date or datetime field that can be set automatically as current time
define('CREATION_DATE', 'creation_date');

// User Token
define('SECRET_KEY', 'bGS6lzFqvvSQ8ALbOxatm7/Vk7mLQyzqaS34Q4oR1ew=');
define('TOKEN_ENC_ALGORITHM', 'HS512'); 
define('TOKEN_DURATION', '6 months'); // minutes, hours, days, months, years
define('PASSWORD_ENC_ALGORITHM', 'tiger192,4');


