<?php

// Name of the relate table in the field
// i.e: id_area the table is "area"
//      id_logger the table is "logger"
define('RELATED_TABLE_FROM_ID', '/^id_([\S]*)/'); 

// Index to the same table i.e: "id_parent"
// If the field name contains the same table is not necessary SAME_TABLE_ID
// i.e: if the table is "page" che one field is id_page, then the SAME_TABLE_ID is not used 
define('SAME_TABLE_ID', 'parent'); 

// Convetional id primary key name
define('ITEM_ID', 'id'); // probable alternative: '[table]_id', '[table]ID', 'id_[table]'

// Convetional related field id naming
// to convert the indexes of related tables 
// with a more meaningful value, such as a name
define('RELATED_TABLE_ID', 'id_[related_table]');

// Upload destination path
define ('UPLOAD_FILE_PATH', '/var/www/upload/');

// Type accepted during upload file, the type is case insensitive
define ('UPLOAD_EXT_ACCEPTED', ['doc','docx','pdf','odf','pptx']);

// if is omit "order-by/id", "order-by/id-ASC" or its abbreviation "ob/id-ASC"
define('DEFAULT_ORDER', 'DESC'); // DESC or ASC

// Default of total rows for each request
define('DEFAULT_ITEMS_PER_PAGE', 20);

// Max total rows for each request, if the value is greater it will be reset to MAX_ITEMS_PER_PAGE value
define('MAX_ITEMS_PER_PAGE', 200);
