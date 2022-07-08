<?php
/**
 * 
 * DressAPI
 * @version 1.1
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @copyright Tufano Pasquale
 * @url https://dressapi.com
 * 
 * 
 * Basic functions for the connection and management of the db
 *
 */


namespace DressApi\core\dbms;

use Exception;

/**
 * Class to manage a Generic DataBase
 */
abstract class CDBMS
{
    // possible enum in future
    const EMERGENCY = 7;
    const ALERT     = 6;
    const CRITICAL  = 5;
    const ERROR     = 4;
    const WARNING   = 3;
    const NOTICE    = 2;
    const INFO      = 1;
    const DEBUG     = 0;

    protected static ?string $dbkey = null;                 // index of the current db
    protected static array $handle          = [];   // list of DB handles
    protected static array  $dbname         = [];   // DB names
    protected static array  $results        = [];   // pointer to the result of a DB
    protected string        $table          = '';   // contains the name of a table
    protected int           $last_id = 0;           // last id inserted

    protected static mixed  $stmt           = null;


    // DB connection parameters
    protected static string $database_name;
    protected static string $dbusername;
    protected static string $dbpassword;
    protected static string $hostname;
    protected static int    $port;

    /**
     * Constructor
     *
     * @param string $table name of the default db table (it can be emtpy)
     */
    public function __construct(string $table = '')
    {
        $this->setDBTable($table);
    }


    /**
     * Destroys any allocated resources
     */
    public function __destruct()
    {
    }


    /**
     *
     * Make a connection to the DB
     *
     * @param string $dbname name of the database inside the DBMS
     * @param string $username name of the user connecting to the DB
     * @param string $password DB password
     * @param string $hostname name or IP address of the server (if it is on the same computer it is usually in "localhost")
     * @param int|string $port communication port: if left empty, it takes the default value (3306)
     * 
     * @return bool true if the DB is connected
     */
    public static function connect(string $dbname, string $username, string $password, string $hostname = 'localhost', int $port = DB_PORT): bool
    {
        try
        {
            self::$dbkey = $hostname . ':' . $port . '.' . $dbname . '.' . $username;

            self::$hostname = $hostname;
            self::$port = $port;
            self::$database_name = $dbname;
            self::$dbusername = $username;
            self::$dbpassword = $password;

            self::$results[self::$dbkey] = null;

            self::$dbname[self::$dbkey] = $dbname;
        }
        catch (\Exception $ex)
        {
            self::critical(__FILE__ . ':' . __LINE__ . ' - ' . $ex->getMessage());
            return false;
        }
        return true;
    }


    /**
     * Return the current date time for this DB
     *
     * @return the current date time for this DB
     */
    public static function getCurrentDateTime(): string
    {
        return date('Y-m-d H:i:s');
    }


    /**
     * Return the current date for this DB
     *
     * @return the current date for this DB
     */
    public static function getCurrentDate(): string
    {
        return date('Y-m-d');
    }


    /**
     * Releases the resource to connect to the DB
     * 
     * @param ?string $dbkey key of db to disconnect (optional), 
     *                if not send or is null keep the current db.
     *                Normally you have only one DB, in this case 
     *                you can ignore $dbkey parameter
     */
    abstract public static function disconnect(?string $dbkey = null);


    /**
     * Releases all the resources of the previously opened DBs
     */
    abstract public static function disconnectAll();


    /**
     * Returns the constant DB_ASSOC which establishes 
     * that the query must return an associative array
     */
    abstract public function getDBResultAssoc();


    /**
     * Returns the constant DB_ASSOC which establishes 
     * that the query must return a numeric array
     */
    abstract public function getDBResultNum();


    /**
     * Returns the constant DB_ASSOC which establishes 
     * that the query must return both an associative array 
     * and a numeric array
     */
    abstract public function getDBResultBoth();


    /**
     *
     * Check the connection to the DB, if not there, try to connect
     *
     */
    // abstract protected static function _realConnection();


    /**
     * Gets any error issued by the DB
     *
     * @return string containing the error issued by the DB
     */
    abstract public static function getLastDBErrorString(): string;


    /**
     * Set autocommit, as opposed to transitions, 
     * if true each operation is self-consistent and validated
     * 
     * @param bool $bool if is true the autocommit is running
     *        Note: if you use the transitions with you must be set to "false"
     * 
     * @see beginTransaction()
     * @see commit();
     * @see rollback();
     * 
     */
    abstract public function setAutoCommit(bool $bool = true);


    /**
     * Start DB transaction
     * It is recommended to use transitions in case of multiple SQL statements, 
     * which, if at least one fails, the entire set of statements can be canceled
     *
     * NOTE: before use benginTransaction() you must be sure that the AutoCommit is false
     *       with setAutoCommit(false)
     * 
     * @see setAutoCommit()
     * @see commit();
     * @see rollback();
     * 
     */
    abstract public function beginTransaction();


    /**
     * Validate SQL operations performed after calling beginTransaction()
     *  
     * @see setAutoCommit()
     * @see beginTransaction()
     * @see rollback();
     * 
     */
    abstract public function commit();


    /**
     * Cancel all SQL operations performed after calling beginTransaction ()
     * 
     * @see setAutoCommit()
     * @see beginTransaction()
     * @see commit();
     * 
     */
    abstract public function rollback();


    /**
     * Make an entry in the DB
     *
     * @param string $table string containing the insert to execute
     * @param array $items associative array containing FIELD_NAME => FIELD_VALUE of the table
     * @param array $types: type of DB table fields (INT, FLOAT, VARCHAR, BLOB, ecc.)
     * 
     * @return true if the insertion has been made
     */
    abstract public function insertRecord(string $table, array $items, array $types): bool;


    /**
     * Make an update in the DB
     *
     * @param string $table string containing the update to be performed
     * @param string $conditions string containing the conditions of the update whose values ​​are replaced by a "?"
     * @param array $items associative array containing FIELD_NAME => FIELD_VALUE of the table
     * @param array $conditions_values array containing the values ​​of the conditions declared in $conditions
     * @param array $types: type of DB table fields (INT,FLOAT,VARCHAR,BLOB,ecc.)
     * 
     * @return true if the insertion has been made
     */
    abstract public function updateRecord(?string $table, string $conditions, array $items, array $conditions_values, array $types);


    /**
     * Delete records from a DB table
     *
     * @param string $table name of the database table on which to delete one or more records
     * @param string $conditions conditions of the where with any "?" in the parameters
     * @param array  $conditions_values values ​​present in the WHERE condition that will have to replace the "?"
     * @param array  $types: type of DB table fields (INT,FLOAT,VARCHAR,BLOB,ecc.)
     * 
     * @return bool result of the operation, true if successful
     */
    abstract public function deleteRecord(?string $table, string $conditions, array $conditions_values, array $types): bool;


    /**
     * Returns the from value of the next id to use in the insert
     * NOTE: in Mysql you can set the primary key as autoincrement, 
     *       than is not needed to set the table and it returns always null
     *
     * @param string $table the name of counter associated to SEQUENCE  
     *
     * @return ?int the next ID of the table
     */
    abstract public function getNextID(string $table = ''): ?int;


    /**
     * Returns the ID of the last inserted item
     *
     * @return ?int if the operation was successful, it returns the id of the last element inserted
     */
    public function getLastID(): int { return $this->last_id; }


    /**
     * Returns how many rows were affected by the last operation performed
     *
     * @return int total of rows affected by the last operation
     */
    abstract public function getAffectedRows(): int;


    /**
     * Reads the total of rows of a previously executed query
     *
     * @return int|bool the total number of rows which make up a table generated by the last query executed 
     *                  or return false in case of error
     */
    abstract public function getTotalRows(): int|bool;


    /**
     * Returns the number of columns of a previously executed query
     *
     * @return int total of the columns that make up a table generated by the last query executed, FALSE in case of error
     */
    abstract public function getTotalColumns(): int|bool;


    /**
     * Returns an array containing the names of all columns of $tablename
     *
     * @return ?array an array with all fields
     *                or null if the table was not found
     */
    abstract public function getTableColumnsName($tablename): array;

    /**
     * Returns an array containing the names of all columns for the last query executed
     *
     * @return ?array the associative having as index and value the column names of the queries or null if it fails
     *                or null if the table was not found
     */
    abstract public function getColumnsName(): ?array;


    /**
     * Reads the error number issued by the DB
     *
     * @return int integer value containing the error issued by the DB
     */
    abstract public static function getLastDBErrorNumber(): int;


    /**
     * Returns an array containing the list of all the tables of the current DB
     *
     * @param ?string $table base table name to get all those that use it
     * @param bool $with_sizes if "false" it does not calculate the total of the records (by default it is true)
     * @return array information relating to the tables in the DB
     */
    abstract public function getInfoAllTables(?string $table = null, bool $with_sizes = false): array;


    /**
     * Query the DB with SQL
     *
     * @param string $sql string containing the query to execute
     * @param array $bind_param_values deferred values ​​of the query
     * @param array $bind_param_types DB data types
     * @return bool true if the query was successful
     */
    abstract public function query(string $sql, array $bind_param_values = null, array $bind_param_types = null): bool;


    /**
     * Prepares an SQL statement for execution
     *
     * @param string $sql string containing the query to execute
     * 
     */
    abstract public function prepare(string $sql): void;


    /**
     * Execution of SQL with binding
     * 
     * @param array $bind_param_values
     * @param array $bind_param_types
     * 
     */
    abstract public function execute(?array $bind_param_values = null, ?array $bind_param_types = null): void;


    /**
     * Reads a row of the position database $pos_row of the table of the previously executed query 
     * and returns it as an array with both numeric and associative numbers
     *
     * @param ?int $pos_row position of the line to read, if omitted is the current element
     * @return ?array the array of the row indicated by $ pos_row (having both a numeric index and containing the column name)
     *               or null where the result is not valid (e.g. if the index is outside the range of the table)
     */
    abstract public function getFetchArray(?int $pos_row = null): ?array;


    /**
     * Gets a row of the position database $pos_row of the table of the previously executed query and 
     * returns it as an associative array in an associative array whose index is given by the name of the column
     *
     * @param ?int $pos_row position of the line to read, the current element if it is omitted
     * @return ?array associative array of the row indicated by $pos_row 
     *                or null in which the result is not valid 
     *                e.g.: if the index is outside the range of the table
     */
    abstract public function getFetchAssoc(?int $pos_row = null): ?array;


    /**
     * Gets a row of the $pos_row position db of the previously executed query table 
     * and returns it as an array with a numeric index
     *
     * @param ?int $pos_row posizione della riga da leggere, se omesso e' l'elemento corrente
     * @return ?array the array of the indicated row (having a numeric index) from $pos_row 
     *               or null in which the result is not valid 
     *               (e.g. if the index is outside the range of the table)
     */
    abstract public function getFetchRow(?int $pos_row = null): ?array;


    /**
     * Returns a query value relative to a row and a column
     *
     * @param int|string $name_col column name or numeric index (default 0)
     * @param ?int $pos_row position of the row starting from 0, if it is null or not indicated it reads the current value
     * @return ?string value of the indicated row and column relative to the previously executed query, null if it does not exist
     */
    abstract public function getDataValue(int|string $name_col = '0', ?int $pos_row = null): ?string;


    /**
     * Given a previously executed query, it returns the result in a two-dimensional array
     *
     * @param array $data array containing the records found
     * @param int $type it can assume the values: DB_ASSOC, DB_NUM and DB_BOTH equivalent to the type of array it must return, 
     *                  respectively: 
     *                    - numeric index (returned from getDBResultNum())
     *                    - associative index (returned from getDBResultAssoc())
     *                    - both indices (returned from getDBResultBoth())
     *                  by default is value returned from getDBResultAssoc(), that is, it returns an associative array
     * @param ?string $with_index indexes of the output array (for example it could be "id"), 
     *                if specified the numeric value of the row is replaced with the value indicated in the index
     * @return int the total of records found
     */
    abstract public function getDataTable(array &$data, ?int $type = null, ?string $with_index = null): int;


    /**
     * Returns an array containing the table name as an index and 
     * an array containing the name and type of the columns as a value
     *
     * @param array list of tables to be returned
     */
    abstract public function getAllTables(array &$db_tables);


    /**
     * Returns an array containing the list of columns of a table
     * each columns contains: 
     *    - 'field' the name of column,
     *    - 'type' the type of column,
     *    - 'null' if accept the NULL DB value,
     *
     * @param array $db_cols the results to be returned
     * @param array $table the table to be sampled, 
     *                     if not specified or null, uses the previously set table
     */
    abstract protected function setDBColumns(array &$db_cols, $table = null);


    /**
     * Check if the table passed as a parameter exists (including the prefix)
     *
     * @param string $table name of the table
     * @return bool true if it exists, false if it does not exist
     */
    abstract public function ExistsTable(string $table): bool;


    /**
     * Check if a named list of fields exists in a db table 
     * 
     * @param string $table name of table
     * @param array $names list of field to check
     * 
     * @return bool true if exists all fields
     */
    abstract protected function existsTableColumns(?string $table, array $names): bool;


    /**
     * Reversibly encrypts a value to DB
     * 
     * NOTE: if $val==$name encrypts the name of the DB field
     *
     * @param string $val value or name of the field to be encrypted
     * @param string $name name of the field that is returned
     * @return string DB instruction to encrypt $val
     */
    abstract public function encrypt(string $val, $name = ''): string;


    /**
     * Decrypt a value from DB
     * 
     * NOTE: if $val==$name decrypts the DB field name
     *
     * @param string $val value or name of the field to decrypt
     * @param string $name name of the field that is returned
     * @return string DB instruction to decrypt $ val
     */
    abstract public function decrypt(string $val, $name = ''): string;


    /**
     *
     * Set the current db key (if you have more than on DB)
     *
     * @param $dbkey the new dbkey for identify the DB connection if exists
     *
     * @throws Exception if $dbkey value is not a valid key
     */
    abstract protected static function getConnectionError(?string $dbkey = null) : int;


    /**
     * Restituisce la chiave del DB corrente (da usare al posto di DB_NAME in progetti precedenti)
     */
    public function getCurrentDBKey(): string
    {
        return self::$dbkey;
    }


    /**
     * Set the current DB key
     * NOTE: generally it is not necessary to use a DB key but it can be useful when there are 
     *       multiple databases to manage
     *
     * @param string $dbkey valore della chiave del DB da impostare
     * @return true se la chiave del db è valida
     */
    public function setCurrentDBKey(string $dbkey): bool
    {
        $valid = true;
        if (isset(self::$handle[$dbkey]))
        {
            self::$dbkey = $dbkey;
            $valid = false;
        }
        return $valid;
    }


    /**
     * Queries the DB and returns an associative array containing the result
     *
     * @param string $sql string containing the query to execute
     * @param ?int $pos_row position of the row starting from 0, if it is null 
     *             or not indicated it reads the current value
     * @return ?array the associative array of the row indicated by $ pos_row 
     *             or null if the result is not valid (e.g. if the index is outside the range of the table)
     */
    public function getQueryFetchAssoc(string $sql, ?int $pos_row = null): ?array
    {
        return (($this->query($sql) !== null) ?
            $this->getFetchAssoc($pos_row) : null);
    }


    /**
     * Queries the DB and returns a row from the DB as an array having a numeric index
     *
     * @param string $sql string containing the query to execute
     * @param ?int $pos_row position of the row starting from 0, 
     *             if it is null or not indicated it reads the current value
     * @return array the array of the indicated row (having a numeric index) from $pos_row 
     *               or null in which the result is not valid 
     *               (e.g. if the index is outside the range of the table)
     */
    public function getQueryFetchRow(string $sql, ?int $pos_row = null): ?array
    {
        return (($this->query($sql) !== null) ? 
            $this->getFetchRow($pos_row): null);
    }


    /**
     * Creates an array containing all elements found based on a DB table name or index 
     *
     * @param int|string $item index (int or string) of array to be returned
     * @param int|string|null $idx name or index of the field (by default it is 0, which is the first element)
     * @return ?array containing all values ​​indicated by $idx or null if there is no data or an error has occurred
     */
    public function getIdsArray(int|string $item = 0, int|string|null $idx = null): ?array
    {
        $elements = [];
        $this->getDataTable($elements, $this->getDBResultBoth());

        if ($elements === [])
            $v = null;
        else
        {
            $v = [];
            foreach ($elements as $element)
                if (isset($element[$item]))
                {
                    if ($idx === null || !isset($element[$idx]))
                        $v[] = $element[$item];
                    else
                        $v[$element[$idx]] = $element[$item];
                }
        }
        return $v;
    }


    /**
     * Queries the DB and returns a specific value
     *
     * @param string $sql string containing the query to execute
     * @param string $name_col column name or numeric index (default 0)
     * @param ?int $pos_row position of the row starting from 0, if it is null or not indicated it reads the current value
     * @return ?string indicated value of the row and column (by default it is the first column of current element)
     */
    public function getQueryDataValue(string $sql, string $name_col = '0', ?int $pos_row = null): ?string
    {
        $this->query($sql);

        return $this->getDataValue($name_col, $pos_row);
    }


    /**
     * Given a previously executed query, it returns the result in a two-dimensional array
     *
     * @param array $data array containing the records found
     * @param string $sql string containing the query to execute
     * @param ?array $bind_param_values deferred values ​​of the query
     * @param ?array $bind_param_types DB data types
     * @param ?int $type it can assume the values: CDB::DB_ASSOC, CDB::DB_NUM and CDB::DB_BOTH equivalent to the type of array it must return, 
     *                  respectively: 
     *                    - numeric index (returned from getDBResultNum())
     *                    - associative index (returned from getDBResultAssoc())
     *                    - both indices (returned from getDBResultBoth())
     *                  by default is value returned from getDBResultAssoc(), that is, it returns an associative array
     * @param ?string $with_index indexes of the output array (for example it could be "id"), 
     *                if specified the numeric value of the row is replaced with the value indicated in the index
     * @return int the total of records found
     */
    public function getQueryDataTable(array &$data, string $sql, 
                                      ?array $bind_param_values = null, ?array $bind_param_types = null,  
                                      ?int $type = null, ?string $with_index = null): int
    {
        if ($type === null)
            $type = $this->getDBResultAssoc();

        $this->query($sql, $bind_param_values, $bind_param_types);

        return $this->getDataTable($data, $type, $with_index);
    }


    /**
     * After executing a query it returns an array whose row index has the column value "$ name" and the rest of the fields are the cell value:
     *
     * @param string $name name of the select column that must have as index (default is' 'name')
     * @param ?string $fieldname_value name of the field to fetch the value from, if null returns all data in an array
     * @return ?array array containing the list name => value in which value can be a scalar or an associative array if it must contain more values
     *                      or it returns null if there are no elements or an empty array if the field name does not exist among the results of the last query executed
     * @example if the sql returns:
     *               name   val
     *               ==========
     *                Max    10
     *                Min     1
     * The getArrayByName('name','val') function will return array('Max'=>10,'Min'=1)
     *
     * @example if the sql returns:
     *               id  name  val
     *               ============
     *               1  Max    10
     *               2  Min      1
     * The getArrayByName('name') function will return array('Max'=>array('id'=>1,'val'=>10),'Min'=array('id'=>2,'val'=>1))
     */
    public function getArrayByName(string|int $name = 'name', ?string $fieldname_value = null): ?array
    {
        $elements = [];
        if (!$this->getDataTable($elements, $this->getDBResultBoth()))
            $v = null;
        else
        {
            $v = [];
            foreach ($elements as $element)
                if (isset($element[$name]))
                {
                    $idx = $element[$name];
                    unset($element[$name]);
                    if ($fieldname_value !== null && isset($element[$fieldname_value]))
                        $v[$idx] = $element[$fieldname_value];
                    elseif (count($element) > 1)
                        $v[$idx] = $element;
                    else
                        $v[$idx] = array_shift($element);
                }
        }
        return $v;
    }


    /**
     * Given an array containing a series of records, 
     * it returns an array containing only the values ​​of a specific field
     *
     * @param array $elements all records to be analyzed
     * @param string $fieldname name of the field / column containing the data to be extrapolated
     * @return ?array array containing the list of values ​​found or null if it found nothing
     */
    public function getArrayItemOfElements(array &$elements, string $fieldname): ?array
    {
        $result = null;
        if (isset($elements))
            foreach ($elements as $element)
                if (isset($element[$fieldname]))
                    $result[$element[$fieldname]] = $element[$fieldname];

        return $result;
    }


    /**
     * Returns the current handle of the DB
     *
     */
    public static function getDBHandle()
    {
        return self::$handle[self::$dbkey] ?? null;
    }


    /**
     * Sets the name of the reference table
     *
     * @param string $table name of the main table to set
     */
    public function setDBTable(string $table)
    {
        $this->table = $table;
    }


    /**
     * Returns the name of the current main table
     *
     * @return string name of the main reference table
     */
    public function getDBTable(): string
    {
        return $this->table;
    }


    /**
     *
     * Check the connection is active without error
     *
     * @param $dbkey valid key for identify the DB connection (if null or not declared the key is of current db)
     *               Is useful when you have more than one DB, in other case you don't need to use
     * 
     */
    protected static function isConnected(?string $dbkey = null)
    {
        if ($dbkey===null)
            $dbkey = self::$dbkey;
        return (!isset(self::$handle[$dbkey]) || self::getConnectionError($dbkey));
    }


    /**
     *
     * Get the current db key
     *
     * @return $dbkey key for identify the DB connection
     * 
     */
    protected static function getCurrentConnectionKey() : ?string
    {
        return self::$dbkey;
    }


    /**
     *
     * Set the current db key (if you have more than on DB)
     *
     * @param $dbkey the new dbkey for identify the DB connection if exists
     *
     * @throws Exception if $dbkey value is not a valid key
     */
    protected static function setCurrentConnectionKey(string $dbkey) : void
    {
        if (isset(self::$handle[$dbkey]) && !self::getConnectionError($dbkey))
            self::$dbkey = $dbkey;
        else
        {
            $message = 'The db key is not valid, the current db is unchanged';
            self::error($message);
            throw new Exception($message);
        }
    }


    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function emergency(string $message, array $context = [])
    {
        self::log(self::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function alert(string $message, array $context = [])
    {
        self::log(self::ALERT, $message, $context);
    }


    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function critical(string $message, array $context = [])
    {
        self::log(self::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function error(string $message, array $context = [])
    {
        self::log(self::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function warning(string $message, array $context = [])
    {
        self::log(self::WARNING, $message, $context);
    }


    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function notice(string $message, array $context = [])
    {
        self::log(self::NOTICE, $message, $context);
    }


    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function info(string $message, array $context = [])
    {    
        self::log(self::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function debug(string $message, array $context = [])
    {   
        self::log(self::DEBUG, $message, $context);
    }


    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function log(mixed $level, string $message, array $context = []) : void
    {
        $levels = [ self::EMERGENCY => 'emergency', self::ALERT    => 'alert',
                    self::CRITICAL => 'critical',   self::ERROR  => 'error',
                    self::WARNING=> 'warning',      self::NOTICE => 'notice',
                    self::INFO => 'info',           self::DEBUG=>'debug'];
    
        if (is_int($level)) // 6=INFO, 7=DEBUG
        {
            if ($level>=LOG_LEVEL_MIN)
                $level_text = $levels[$level];
            else
                return;
        }
        else
            $level_text = $level;
        $path = realpath(__DIR__ . '/../../');
        
        if ($level==self::INFO || $level==self::DEBUG || 
            $level==$levels[self::INFO] || $level==$levels[self::DEBUG]) // 6=INFO, 7=DEBUG
            $filename = $path . '/logs/dressapi-info.log';
        else
            $filename = $path . '/logs/dressapi-errors.log';
        $datarow =  date('Y-m-d H:i:s') . ' - '.$level_text . ' - ' . $message . (($context) ? ('') : (print_r($context, true))) . "\r\n";

        file_put_contents($filename, $datarow, LOCK_EX | FILE_APPEND);
    }
}
