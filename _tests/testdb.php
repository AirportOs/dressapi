<?php

require_once __DIR__ . '/../../Core/autoload.php'; // Autoloader dell'applicazione

define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_NAME', 'cardcomics');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');

use DressApi\Core\DBMS\CMySqlDB as CDB;       // In the future other DBMS as Oracle, PostgreSQL, MS SQL
use DressApi\Core\DBMS\CMySqlDB;

try
{
    // Create connection
    CDB::connect(DB_NAME, DB_USERNAME, DB_PASSWORD, DB_HOST, DB_PORT);

    $db = new CMySqlDB();

    echo "<h2>List of Tables</h2>";
    $db_tables = [];
    $db->getAllTables($db_tables);

    if ($db_tables === null)
        echo "There are no tables<br />\n";
    else
    {
        foreach ($db_tables as $table=>$fields)
        {
            print "TABLE ".$table . "\n=====================\n";
            print_r($fields);
        }
    }
}
catch (Exception $e)
{
    print "ERROR<br>\n";
}

CDB::disconnect();