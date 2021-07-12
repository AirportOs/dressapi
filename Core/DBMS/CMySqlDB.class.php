<?php
/**
 * 
 * DressAPI
 * @version 1.0
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @url https://dressapi.com
 * 
 * 
 * Base class for connection and management of the MySql db
 * 
 */

namespace DressApi\Core\DBMS;

use Exception;

// require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/config.php'; // DB CONFIGURATION


/**
 * Class to manage a DataBase in MySql
 */
class CMySqlDB extends CDBMS
{

    public const DB_ASSOC = MYSQLI_ASSOC;
    public const DB_NUM   = MYSQLI_NUM;
    public const DB_BOTH  = MYSQLI_BOTH;

    protected static bool $autocommit = true;

    /**
     * Returns the constant DB_ASSOC which establishes 
     * that the query must return an associative array
     */
    public function getDBResultAssoc()
    {
        return self::DB_ASSOC;
    }


    /**
     * Returns the constant DB_ASSOC which establishes 
     * that the query must return a numeric array
     */
    public function getDBResultNum()
    {
        return self::DB_NUM;
    }


    /**
     * Returns the constant DB_ASSOC which establishes 
     * that the query must return both an associative array 
     * and a numeric array
     */
    public function getDBResultBoth()
    {
        return self::DB_BOTH;
    }


    /**
     * Releases the resource to connect to the DB
     * 
     * @param ?string $dbkey key of db to disconnect (optional), 
     *                if not send or is null keep the current db.
     *                Normally you have only one DB, in this case 
     *                you can ignore $dbkey parameter
     */
    public static function disconnect(?string $dbkey = null)
    {
        if ($dbkey === null)
            $dbkey = self::$dbkey;

        if (isset(self::$handle[$dbkey]))
            $mysqli = self::$handle[$dbkey];

        if (isset($mysqli) && !$mysqli->connect_errno)
        {
            $mysqli->close();
            unset(self::$handle[$dbkey]);
        }
    }


    /**
     * Releases all the resources of the previously opened DBs
     */
    public static function disconnectAll()
    {
        if (isset(self::$handle))
            foreach (self::$handle as $name => $obj)
                if (self::$handle[$name] && !self::$handle[$name]->connect_errno)
                {
                    self::$handle[$name]->close();
                    unset(self::$handle[$name]);
                }
    }

    /**
     *
     * Check the connection to the DB, if not there, try to connect
     *
     */
    protected static function _realConnection()
    {
        if (!isset(self::$handle[self::$dbkey]) || self::$handle[self::$dbkey]->connect_error)
        {
            self::$handle[self::$dbkey] = new \mysqli(
                self::$hostname,
                self::$username,
                self::$password,
                self::$database_name,
                self::$port
            );
            self::$handle[self::$dbkey]->autocommit(self::$autocommit);
        }

        return (isset(self::$handle[self::$dbkey]) && !self::$handle[self::$dbkey]->connect_error);
    }


    /**
     * Gets any error issued by the DB
     *
     * @return string containing the error issued by the DB
     */
    public static function getLastDBErrorString(): string
    {
        return self::$handle[self::$dbkey]?->errno;
    }


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
    public function setAutoCommit(bool $bool = true)
    {
        self::$autocommit = $bool;
        if (isset(self::$handle[self::$dbkey]))
            self::$handle[self::$dbkey]->autocommit($bool);
    }


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
    public function beginTransaction()
    {
        if (isset(self::$handle[self::$dbkey]))
            self::$handle[self::$dbkey]->begin_transaction();
    }


    /**
     * Validate SQL operations performed after calling beginTransaction()
     *  
     * @see setAutoCommit()
     * @see beginTransaction()
     * @see rollback();
     * 
     */
    public function commit()
    {
        if (isset(self::$handle[self::$dbkey]))
            self::$handle[self::$dbkey]->commit();
    }


    /**
     * Cancel all SQL operations performed after calling beginTransaction ()
     * 
     * @see setAutoCommit()
     * @see beginTransaction()
     * @see commit();
     * 
     */
    public function rollback()
    {
        if (isset(self::$handle[self::$dbkey]))
            self::$handle[self::$dbkey]->rollback();
    }



    /**
     * Make an entry in the DB
     *
     * @param string $table string containing the insert to execute
     * @param array $items associative array containing FIELD_NAME => FIELD_VALUE of the table
     * @param array $types: type of DB table fields (INT, FLOAT, VARCHAR, BLOB, ecc.)
     * 
     * @return true if the insertion has been made
     */
    public function insertRecord(string $table, array $items, array $types): bool
    {
        $ret = true;
        if (!self::_realConnection())
            $ret = false;
        else
        {
            $mysqli = self::$handle[self::$dbkey];

            if ($table === null)
                $table = $this->table;
  
            $items_keys = array_keys($items);
            array_walk($items_keys, function (&$value)
            {
                $value = "`$value`";
            });
            $items_values = array_values($items);
            $items_qvalues = array_fill(0, count($items_keys), '?');

            $stypes = '';
            if (isset($types))
                foreach ($types as $db_type)
                    $stypes .= $this->convertDBType2BindType($db_type);

            $sql = "INSERT INTO $table (" . implode(",", $items_keys) . ") " . "VALUES (" . implode(",", $items_qvalues) . ")";

            try
            {
                $stmt = $mysqli->prepare($sql); // "INSERT INTO test(id, label) VALUES (?, ?)"
                if ($items_values !== null)
                    $stmt->bind_param($stypes, ...$items_values);
                $ret = $stmt->execute();
                if (!$ret) 
                    self::writeLog("#### ERROR ($sql): " . $stmt->error);
                $stmt->close();
            }
            catch (Exception $e)
            {
                $ret = false;
                self::writeLog("#### EXCEPTION ($sql): " . $e->getMessage());
            }
        }
        return $ret;
    }


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
    public function updateRecord(?string $table, string $conditions, array $items, array $conditions_values, array $types)
    {
        $ret = true;
        if (!self::_realConnection())
            $ret = false;
        else
        {
            $mysqli = self::$handle[self::$dbkey];

            if ($table === null)
                $table = $this->table;

            $items_keys = array_keys($items);
            array_walk($items_keys, function (&$value, $key)
            {
                $value = "`$value`=?";
            });

            $items_values = array_values($items);

            if (isset($conditions_values))
                $items_values = array_merge($items_values, $conditions_values);

            $stypes = '';
            if (isset($types))
                foreach ($types as $db_type)
                    $stypes .= $this->convertDBType2BindType($db_type);

            try
            {
                $sql = "UPDATE `$table` SET " . implode(",", $items_keys) . " WHERE $conditions";
                $stmt = $mysqli->prepare($sql);
                // echo "\n$sql ($types) ".print_r($items_values,true)."\n";

                if ($items_values !== null)
                    $stmt->bind_param($stypes, ...$items_values);
                $ret = $stmt->execute();
                $stmt->close();

                if (self::getLastDBErrorString())
                {
                    $ret = false;
                    self::writeDebug("#### ERROR: " . $this->getLastDBErrorString());
                }
            }
            catch (Exception $e)
            {
                $ret = false;
                self::writeLog("#### EXCEPTION ($sql): " . $e->getMessage());
            }
        }

        return $ret;
    }


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
    public function deleteRecord(?string $table, string $conditions, array $conditions_values, array $types): bool
    {
        $ret = true;
        if (!self::_realConnection())
            $ret = false;
        else
        {
            try
            {
                if ($table === null)
                    $table = $this->table;

                $mysqli = self::$handle[self::$dbkey];

                $stypes = '';
                if (isset($types))
                    foreach ($types as $db_type)
                        $stypes .= $this->convertDBType2BindType($db_type);

                $sql = "DELETE FROM $table WHERE $conditions";
                $stmt = $mysqli->prepare($sql);

                $stmt->bind_param($stypes, ...array_values($conditions_values));
                $ret = $stmt->execute();
                $stmt->close();

                if (self::getLastDBErrorString())
                {
                    $ret = false;
                    self::writeDebug("#### ERROR: " . $this->getLastDBErrorString());
                }
            }
            catch (Exception $e)
            {
                $ret = false;
                self::writeLog("#### EXCEPTION ($sql): " . $e->getMessage());
            }
        }

        return $ret;
    }


    /**
     * Returns the from value of the next id to use in the insert
     * NOTE: in Mysql you can set the primary key as autoincrement, 
     *       than is not needed to set the table and it returns always null
     *
     * @param string $table the name of counter associated to SEQUENCE  
     *
     * @return ?int the next ID of the table
     */
    public function getNextID(string $table = ''): ?int
    {
        return null;
    }


    /**
     * Returns the ID of the last inserted item
     *
     * @param null $table name of the reference table
     * @return ?int if the operation was successful, it returns the id of the last element inserted
     */
    public function getLastID(?string $table = null): ?int
    {
        $last_id = null;
        if (isset(self::$handle[self::$dbkey]))
        {
            $mysqli = self::$handle[self::$dbkey];

            if ($table == null && $mysqli->insert_id > 0)
                $last_id = $mysqli->insert_id;
            else
            {
                $sql = "SELECT DISTINCT LAST_INSERT_ID()";
                if ($table != null)
                    $sql .= " FROM `$table`";
                $this->query($sql);
                $last_id = $this->getDataValue(0, 0);
            }
        }
        // var_dump( $last_id );
        return $last_id;
    }


    /**
     * Returns how many rows were affected by the last operation performed
     *
     * @return int total of rows affected by the last operation
     */
    public function getAffectedRows(): int
    {
        return ((self::$handle[self::$dbkey]) ?
            self::$handle[self::$dbkey]->affected_rows : 0);
    }


    /**
     * Reads the total of rows of a previously executed query
     *
     * @return int|bool the total number of rows which make up a table generated by the last query executed 
     *                  or return false in case of error
     */
    public function getTotalRows(): int|bool
    {
        $result = &self::$results[self::$dbkey];
        if ($result)
            return $result->num_rows;
        else
            self::writeLog("#### ERROR: No DB " . self::$dbkey . " found");

        return false;
    }


    /**
     * Returns the number of columns of a previously executed query
     *
     * @return int|bool total of the columns that make up a table generated by the last query executed 
     *                  or boolean "false" in case of error
     */
    public function getTotalColumns(): int|bool
    {
        $result = &self::$results[self::$dbkey] ?? null;
        if ($result)
            return $result->field_count;
        else
            self::writeLog("#### ERROR: No DB " . self::$dbkey . " found");
        return false;
    }


    /**
     * Returns an array containing the names of all columns for the last query executed
     *
     * @return ?array the associative having as index and value the column names of the queries or null if it fails
     *                or null if the table was not found
     */
    public function getColumnsName(): ?array
    {
        $names = null;

        $result = &self::$results[self::$dbkey] ?? null;

        if ($result)
        {
            try
            {
                $tot_cols = $this->getTotalColumns(); // Tot. colonne
                if ($tot_cols > 0)
                {
                    $obj = $result->fetch_fields();
                    $names = [];
                    for ($c = 0; $c < $tot_cols; $c++)   // Nomi colonne
                    {
                        $name_col = $obj[$c]->name;
                        $names[$name_col] = $name_col;
                    }
                }
            }
            catch (Exception $e)
            {
                self::writeLog("#### EXCEPTION: " . $e->getMessage());
            }
        }
        else
            self::writeLog("#### ERROR: No DB " . self::$dbkey . " found");

        return $names;
    }


    /**
     * Reads the error number issued by the DB
     *
     * @return int integer value containing the error issued by the DB
     */
    public function getLastDBErrorNumber(): int
    {
        return $this->handle[$this->dbkey]?->error;
    }


    /**
     * Returns an array containing the list of all the tables of the current DB
     *
     * @param ?string $table base table name to get all those that use it
     * @param bool $with_sizes if "false" it does not calculate the total of the records (by default it is true)
     * @return array information relating to the tables in the DB
     */
    public function getInfoAllTables(?string $table = null, bool $with_sizes = false): array
    {
        $this->getAllTables($db_tables);

        if ($table === null)
            $table = $this->table;

        $info = [];
        if (isset($db_tables)) foreach ($db_tables as $name => $dummy) // Per ogni colonna
            if ($table === null || (strstr($name, '_') && in_array($table, explode('_', $name))))
            {
                if ($with_sizes)
                {
                    $sql = "SELECT COUNT(*) FROM $name";
                    $info[$name] = $this->getQueryDataValue($sql);
                }
                else
                    $info[$name] = $name;
            }

        return $info;
    }


    /**
     * Query the DB with SQL
     *
     * @param string $sql string containing the query to execute
     * @param array $bind_param_values deferred values ​​of the query
     * @param array $bind_param_types DB data types
     * @return bool true if the query was successful
     */
    public function query(string $sql, array $bind_param_values = null, array $bind_param_types = null): bool
    {
        if (!self::_realConnection())
            return false;

        $mysqli = self::$handle[self::$dbkey];

        $ret = false;
        try
        {
            if ((defined('DEBUGGING_SELECT') && DEBUGGING_SELECT) || ((defined('DEBUGGING') && DEBUGGING) && (!stristr($sql, "SELECT") && !stristr($sql, "SHOW ") && !stristr($sql, "session"))))
                self::writeDebug($sql." - [".(($bind_param_values===null)?(''):(print_r($bind_param_values,true)))."]");

            $types = '';
            if ($bind_param_types)
                foreach ($bind_param_types as $bp)
                    $types .= $this->convertDBType2BindType($bp);

            $stmt = $mysqli->prepare($sql); // "INSERT INTO test(id, label) VALUES (?, ?)"
            if ($stmt)
            {
                if ($types != '')
                $stmt->bind_param($types, ...$bind_param_values);
                $ret = $stmt->execute();
                if ($ret)
                    self::$results[self::$dbkey] = $stmt->get_result(); // get the mysqli result                
            }

            if (!$ret)
                self::$results[self::$dbkey] = null;
        }
        catch (Exception $e)
        {
            self::writeLog("#### EXCEPTION ($sql): " . $e->getMessage());
        }

        if (self::getLastDBErrorString())
        {
            self::writeLog("#### ERROR: " . $this->getLastDBErrorString());
        }

        return $ret;
    }


    /**
     * Prepares an SQL statement for execution
     *
     * @param string $sql string containing the query to execute
     * 
     */
    public function prepare(string $sql): void
    {
        if (self::_realConnection())
        {
            $mysqli = self::$handle[self::$dbkey];
            $this->stmt = $mysqli->prepare($sql);
        }
        else
            $this->stmt = null;
    }


    /**
     * Execution of SQL with binding
     * 
     * @param array $bind_param_values
     * @param array $bind_param_types
     * 
     */
    public function execute(?array $bind_param_values = null, ?array $bind_param_types = null): void
    {
        if ($this->stmt)
        {
            if (
                $bind_param_values && count($bind_param_values) > 0 &&
                $bind_param_types && count($bind_param_types) > 0
            )
            {
                $types = '';
                if ($bind_param_types)
                    foreach ($bind_param_types as $bp)
                        $types .= $this->convertDBType2BindType($bp);

                $this->stmt->bind_param($types, ...$bind_param_values);
            }

            $this->stmt->execute();
            $this->stmt->close();
        }
    }


    /**
     * Reads a row of the position database $pos_row of the table of the previously executed query 
     * and returns it as an array with both numeric and associative numbers
     *
     * @param ?int $pos_row position of the line to read, if omitted is the current element
     * @return ?array the array of the row indicated by $ pos_row (having both a numeric index and containing the column name)
     *               or null where the result is not valid 
     *               e.g. if the index is outside the range of the table
     */
    public function getFetchArray(?int $pos_row = null): ?array
    {
        $result = &self::$results[self::$dbkey] ?? false;
        if ($result)
        {
            try
            {
                if ($pos_row === null || $result->data_seek($pos_row))
                    return $result->fetch_array($this->getDBResultBoth());
                else
                    self::writeLog("#### ERROR: " . $this->getLastDBErrorString());
            }
            catch (Exception $e)
            {
                self::writeLog("#### EXCEPTION: " . $e->getMessage());
            }
        }
        else
            self::writeLog("#### ERROR: No DB " . self::$dbkey . " found");

        return null;
    }


    /**
     * Gets a row of the position database $pos_row of the table of the previously executed query and 
     * returns it as an associative array in an associative array whose index is given by the name of the column
     *
     * @param ?int $pos_row position of the line to read, the current element if it is omitted
     * @return ?array associative array of the row indicated by $pos_row 
     *                or null in which the result is not valid 
     *                e.g.: if the index is outside the range of the table
     */
    public function getFetchAssoc(?int $pos_row = null): ?array
    {
        $result = &self::$results[self::$dbkey] ?? false;
        if ($result)
        {
            try
            {
                if ($pos_row === null || $result->data_seek($pos_row))
                    return $result->fetch_assoc();
                else
                    self::writeLog("#### ERROR: " . $this->getLastDBErrorString());
            }
            catch (Exception $e)
            {
                self::writeLog("#### EXCEPTION: " . $e->getMessage());
            }
        }

        return null;
    }


    /**
     * Gets a row of the $pos_row position db of the previously executed query table 
     * and returns it as an array with a numeric index
     *
     * @param ?int $pos_row posizione della riga da leggere, se omesso e' l'elemento corrente
     * @return ?array the array of the indicated row (having a numeric index) from $pos_row 
     *               or null in which the result is not valid 
     *               (e.g. if the index is outside the range of the table)
     */
    public function getFetchRow(?int $pos_row = null): ?array
    {
        $result = &self::$results[self::$dbkey] ?? false;
        if ($result)
        {
            try
            {
                if ($pos_row === null || $result->data_seek($pos_row))
                    return $result->fetch_row();
                else
                    self::writeLog("#### ERROR: " . $this->getLastDBErrorString());
            }
            catch (Exception $e)
            {
                self::writeLog("#### EXCEPTION: " . $e->getMessage());
            }
        }
        else
            self::writeLog("#### ERROR: No DB " . self::$dbkey . " found");

        return null;
    }


    /**
     * Returns a query value relative to a row and a column
     *
     * @param int|string $name_col column name or numeric index (default 0)
     * @param ?int $pos_row position of the row starting from 0, if it is null or not indicated it reads the current value
     * @return value of the indicated row and column relative to the previously executed query, null if it does not exist
     */
    public function getDataValue(int|string $name_col = '0', ?int $pos_row = null): ?string
    {
        if (isset(self::$handle[self::$dbkey]))
        {
            $result = &self::$results[self::$dbkey];
            if ($result && ($pos_row === null || ($pos_row >= 0 && $pos_row < $result->num_rows && $result->data_seek($pos_row))))
            {
                try
                {
                    $row = $result->fetch_array($this->getDBResultBoth()); // It works for both numbers and strings
                    if ($row && isset($row[$name_col]))
                        return $row[$name_col];
                }
                catch (Exception $e)
                {
                    self::writeLog("#### EXCEPTION: " . $e->getMessage());
                }
            }
            else
                self::writeLog("#### ERROR: No results found");
        }
        else
            self::writeLog("#### ERROR: No DB " . self::$dbkey . " found");

        return null;
    }
    
    
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
    public function getDataTable(array &$data, ?int $type = null, ?string $with_index = null): int
    {
        if ($type === null)
            $type = $this->getDBResultAssoc();

        $result = &self::$results[self::$dbkey] ?? null;
        $data = [];

        $num_rows = ($result?->num_rows) ?? 0; // Tot. righe

        if ($result)
        {
            try
            {
                if ($num_rows > 0)
                {
                    $result->data_seek(0);

                    if ($with_index == null)
                        $data = $result->fetch_all($type); // Tutti i dati in un colpo solo
                    else
                        for ($r = 0; $r < $num_rows; $r++) // Dati della tabella
                        {
                            $record = $result->fetch_array($type);

                            if (isset($record[$with_index]))
                                $data[$record[$with_index]] = $record;
                            else
                                $data[] = $record;
                        }
                }
            }
            catch (Exception $e)
            {
                self::writeLog("#### EXCEPTION: " . $e->getMessage());
            }
        }
        else
            self::writeLog("#### ERROR: No DB " . self::$dbkey . " found");

        return $num_rows;
    }


    /**
     * Returns an array containing the table name as an index and 
     * an array containing the name and type of the columns as a value
     *
     * @param array list of tables to be returned
     */
    public function getAllTables(array &$db_tables)
    {
        $db_tables = [];

        $this->query("SHOW TABLES");
        //    $db_tables = array_keys($this->getArrayByName(0));

        $db_tables = $this->getArrayByName(0);
        if (isset($db_tables))
            foreach ($db_tables as $table => $dummy)
            {
                $db_cols = [];
                $this->setDBColumns($db_cols, $table);
                $db_tables[$table] = $db_cols;
            }
    }


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
    protected function setDBColumns(array &$db_cols, $table = null)
    {
        if ($table === null)
            $table = $this->table;

        $db_cols = [];

        $sql_cols = "SHOW COLUMNS FROM $table";
        $ret = $this->query($sql_cols);

        if ($ret)
            $cols = self::$results[self::$dbkey];

        if ($ret && $cols?->num_rows)
            while (($a = $cols->fetch_array(MYSQLI_ASSOC)) !== null)
            {
                $name = $a['Field'];
                if (strpos($a['Type'], "("))
                    list($type, $opt,) = explode('(', $a['Type']);
                else
                    $type = $a['Type'];

                $v = ['field' => $name, 'type' => strtoupper($type), 'null' => $a['Null']];

                if ($opt)
                    $opt = str_replace([')', "'"], ['', ''], $opt);

                if ($v['type'] == 'ENUM' || $v['type'] == 'SET')
                    $v['options'] = str_replace(',', '|', $opt);
                elseif (strlen($opt) > 0)
                    $v['max'] = (int)$opt;

                $db_cols[$name] = $v;
            }
    }


    /**
     * Check if the table passed as a parameter exists (including the prefix)
     *
     * @param string $table name of the table
     * @return bool true if it exists, false if it does not exist
     */
    public function ExistsTable(string $table): bool
    {
        return ($this->query("SHOW TABLES LIKE `$table`") > 0);
    }


    /**
     * Return the current date time for this DB
     *
     * @return the current date time for this DB
     */
    public function getCurrentDateTime(): string
    {
        return date('Y-m-d H:i:s');
    }


    /**
     * Return the current date for this DB
     *
     * @return the current date for this DB
     */
    public function getCurrentDate(): string
    {
        return date('Y-m-d');
    }


    /**
     * Check if a named list of fields exists in a db table 
     * 
     * @param string $table name of table
     * @param array $names list of field to check
     * 
     * @return bool true if exists all fields
     */
    protected function existsTableColumns(?string $table, array $names): bool
    {
        if ($table === null)
            $table = $this->table;

        $sql = "SHOW FIELDS FROM `$table`"; // Almeno i campi 'Field,Type,null' es. id,INT(11),YES - Field=[nome del campo], null=YES|NO
        $this->query($sql);
        $db_table = $this->getArrayByName('Field');

        if (!is_array($names))
            return isset($db_table[$names]);
        else
            foreach ($names as $name)
                if (isset($db_table[$name]))
                    return true;
        return false;
    }


    /**
     * Reversibly encrypts a value
     * 
     * NOTE: if $val==$name encrypts the name of the DB field
     *
     * @param string $val value or name of the field to be encrypted
     * @param string $name name of the field that is returned
     * @return string DB instruction to encrypt $val
     */
    public function encrypt(string $val, $name = ''): string
    {
        if ($val != $name) // se non è il nome del campo
            $val = "'" . str_replace("'", "''", $val) . "'";
        return "AES_ENCRYPT($val,UNHEX('" . PWD_CRYPT . "')) $name";
    }


    /**
     * Decrypt a value
     * 
     * NOTA: if $val==$name decrypts the DB field name
     *
     * @param string $val value or name of the field to decrypt
     * @param string $name name of the field that is returned
     * @return string DB instruction to decrypt $ val
     */
    public function decrypt(string $val, $name = ''): string
    {
        if ($val != $name) // if it is not the name of the field
            $val = "'" . str_replace("'", "''", $val) . "'";
        return "AES_DECRYPT($val,UNHEX('" . PWD_CRYPT . "')) $name";
    }

    /**
     * Converts from the type of the DB (INT, VARCHAR, FLOAT, BLOB) to the type of Bind (i, s, d, b)
     *
     * @param string $db_type the type of field of DB 
     * 
     * @return corresponding variable type for binding:
     * 
     *  'i'    corresponding variable has type integer
     *  'd'    corresponding variable has type double
     *  's'    corresponding variable has type string
     *  'b'    corresponding variable is a blob and will be sent in packets
     * 
     * @param string $db_type tipo del db del campo
     * @return handle del risultato della query
     */
    protected function convertDBType2BindType(string $db_type)
    {
        $type = 's';
        if (strpos($db_type, 'INT') !== false)
            $type = 'i';
        elseif (strpos($db_type, 'LOB') !== false) // || strpos($db_type,'TEXT')!==false
            $type = 'b';
        elseif (
            strpos($db_type, 'FLOAT') !== false || strpos($db_type, 'DOUBLE') !== false ||
            strpos($db_type, 'NUMBER') !== false || strpos($db_type, 'DEC') !== false
        )
            $type = 'd';

        return $type;
    }
}
