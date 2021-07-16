<?php
/**
 * 
 * DressAPI
 * @version 1.0
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @copyright Tufano Pasquale
 * @url https://dressapi.com
 * 
 * 
 * Compose a SQL query in a standard SQL 
 */

namespace DressApi\Core\DBMS
{

    /**
     * Class CSqlComposerBase
     *
     * @package DressApi\Core\DBMS
     */
    class CSqlComposerBase
    {
        protected array $params = [];
        protected int $page = 0;
        protected int $elements_per_page = 0;


        /**
         * Constructor
         *
         * @param ?string $table name of the default db table
         */
        public function __construct(?string $table = null)
        {
            $this->clear();
            if ($table !== null) $this->params['TABLE'][0] = $table;
        }


        /**
         * Reset all internal data (like a new)
         */
        public function clear()
        {
            $this->param = [];
            $this->params['ITEMS'] = '*';
            $this->page = 0;
            $this->elements_per_page = 0;
        }


        /**
         * @param string $type type table: TABLE|JOIN|LEFT JOIN
         * @param string $table name of table
         * @param ?string $as alias table name
         * 
         * @return SqlComposerBase $this this object
         */
        private function _getTable($type, string $table, ?string $as = null): CSqlComposerBase
        {
            if (!isset($this->params[$type]))
                $this->params[$type] = [];
            $this->params[$type][] = "`$table` " . ($as ?? '');

            return $this;
        }


        /**
         * @param string $type type table: TABLE|JOIN|
         * @param array $tables list of tables in an array containing the name and possibly its alias name (as)
         * 
         * @return SqlComposerBase $this this object
         */
        private function _getTables($type, array $tables): CSqlComposerBase
        {
            $this->params[$type] = [];
            if (isset($tables))
                foreach ($tables as $table)
                    if (is_string($table))
                        $this->params[$type][] = "`$table` ";
                    else
                        $this->params[$type][] = '`' . $table['name'] . '` `' . $table['as'] ?? '`';

            return $this;
        }


        /**
         * Set the fields of the query
         * 
         * @param string $items list of db fields separated by a comma
         * 
         * @return SqlComposerBase $this this object
         */
        public function select(string $items): CSqlComposerBase
        {
            $this->params['ITEMS'] = $items;

            return $this;
        }


        /**
         * Set the fields of the query
         * 
         * @param array $items array contains the list of db fields
         * 
         * @return SqlComposerBase $this this object
         */
        public function selectList(array $items): CSqlComposerBase
        {
            $this->params['ITEMS'] = implode(',', $items);

            return $this;
        }


        /**
         * @param string $table name of table
         * @param ?string $as alias table name
         * 
         * @return SqlComposerBase $this this object
         */
        public function from(string $table, ?string $as = null): CSqlComposerBase
        {
            return $this->_getTable('TABLE', $table, $as);
        }


        /**
         * @param array $tables list of table name in the query
         * 
         * @return SqlComposerBase $this this object
         */
        public function fromList(array $tables): CSqlComposerBase
        {
            return $this->_getTables('TABLE', $tables);
        }


        /**
         * Sets the name of the table to join and its condition for crossing with a starting table
         * 
         * @param string $join_type type of join (LEFT JOIN, RIGHT JOIN, INNER JOIN, OUTER JOIN)
         * @param string $table name of table
         * @param string $on_condition condition for crossing with a starting table
         * @param ?string $as alias table name
         * 
         * @return SqlComposerBase $this this object
         */
        private function _setJoin(string $join_type, string $table, string $on_condition, ?string $as = null): CSqlComposerBase
        {
            if (!isset($this->params['ON']))
                $this->params['ON'] = [];
            $this->params['ON'][] = $on_condition;

            return $this->_getTable($join_type, $table, $as);
        }


        /**
         * Sets the name of the table to JOIN and its condition for crossing with a starting table
         * 
         * @param string $table name of table
         * @param string $on_condition condition for crossing with a starting table
         * @param ?string $as alias table name
         * 
         * @return SqlComposerBase $this this object
         */
        public function join(string $table, string $on_condition, ?string $as = null): CSqlComposerBase
        {
            return $this->_setJoin('JOIN', $table, $on_condition, $as);
        }


        /**
         * Sets the name of the table to LEFT JOIN and its condition for crossing with a starting table
         * 
         * @param string $table name of table
         * @param string $on_condition condition for crossing with a starting table
         * @param ?string $as alias table name
         * 
         * @return SqlComposerBase $this this object
         */
        public function leftJoin(string $table, string $on_condition, ?string $as = null): CSqlComposerBase
        {
            return $this->_setJoin('LEFT JOIN', $table, $on_condition, $as);
        }


        /**
         * Sets the name of the table to RIGHT JOIN and its condition for crossing with a starting table
         * 
         * @param string $table name of table
         * @param string $on_condition condition for crossing with a starting table
         * @param ?string $as alias table name
         * 
         * @return SqlComposerBase $this this object
         */
        public function rightJoin(string $table, string $on_condition, ?string $as = null): CSqlComposerBase
        {
            return $this->_setJoin('RIGHT JOIN', $table, $on_condition, $as);
        }


        /**
         * Sets the name of the table to INNER JOIN and its condition for crossing with a starting table
         * 
         * @param string $table name of table
         * @param string $on_condition condition for crossing with a starting table
         * @param ?string $as alias table name
         * 
         * @return SqlComposerBase $this this object
         */
        public function innerJoin(string $table, string $on_condition, ?string $as = null): CSqlComposerBase
        {
            return $this->_setJoin('INNER JOIN', $table, $on_condition, $as);
        }


        /**
         * Sets the name of the table to OUTER JOIN and its condition for crossing with a starting table
         * 
         * @param string $table name of table
         * @param string $on_condition condition for crossing with a starting table
         * @param ?string $as alias table name
         * 
         * @return SqlComposerBase $this this object
         */
        public function outerJoin(string $table, string $on_condition, ?string $as = null): CSqlComposerBase
        {
            return $this->_setJoin('OUTER JOIN', $table, $on_condition, $as);
        }


        /**
         * @param string $conditions WHERE conditions
         * 
         * @return SqlComposerBase $this this object
         */
        public function where(string $conditions): CSqlComposerBase
        {
            $this->params['WHERE'] = $conditions;

            return $this;
        }


        /**
         * @param string $group_by list of field names to group by
         * 
         * @return SqlComposerBase $this this object
         */
        public function groupBy(string $group_by): CSqlComposerBase
        {
            $this->params['GROUP_BY'] = $group_by;

            return $this;
        }


        /**
         * @param array $order_by list of field names to order by
         * 
         * @return SqlComposerBase $this this object
         */
        public function orderBy(array $order_by): CSqlComposerBase
        {
            $this->params['ORDER_BY'] = implode(' ', $order_by);

            return $this;
        }


        /**
         * @param string $having conditions for group by having
         * 
         * @return SqlComposerBase $this this object
         */
        public function having(string $having): CSqlComposerBase
        {
            $this->params['HAVING'] = $having;

            return $this;
        }

        /**
         * @param string $match condition for advanced search
         * 
         * @return SqlComposerBase $this this object
         */
        public function match(string $items, string $match): CSqlComposerBase
        {
            if (!isset($this->params['MATCH']))
                $this->params['MATCH'] = [];
            $this->params['MATCH'][$items] = $match;

            return $this;
        }


        /**
         * Set the parameters for the binding
         * 
         * @param ?array $params list of parameters deferred in the query by the placeholder "?"
         */
        public function binding(?array $params = null): void
        {
            $this->params['BINDING'] = $params;
        }


        /**
         * Impostazione dei parametri per l'impaginazione dei dati
         * 
         * @param int page page to read (starting from 1)
         * @param int $elements_per_page maximum total of elements that make up a page
         */
        public function paging(int $page, int $elements_per_page = 20)
        {
            $this->page = $page;
            $this->elements_per_page = $elements_per_page;

            return $this;
        }


        /**
         * @return string the string containing the query made up of all the parameters previously set
         */
        public function __toString(): string
        {
            return $this->prepare();
        }


        /**
         * Only standard code, ignore other dialectic words
         *
         * NOTE: some feature (paging, match,ecc) could be ignored here because you must reimplements prepare() on childs class
         * 
         * @return string the composed SQL code
         */
        public function prepare(): string
        {
            // print_r($this->params);
            $sql = 'SELECT ' . $this->params['ITEMS'] . ' FROM ' . implode(',', $this->params['TABLE']);

            // JOINs
            foreach (['LEFT JOIN', 'OUTER JOIN', 'INNER JOIN', 'RIGHT JOIN'] as $join_type)
                if (!empty($this->params[$join_type]) && !empty($this->params['ON']))
                    foreach ($this->params[$join_type] as $i => $join_table)
                        $sql .= " LEFT JOIN `$join_table` ON " . $this->params['ON'][$i];

            if (isset($this->params['WHERE'])) $sql .= ' WHERE ' . trim($this->params['WHERE']);

            if (isset($this->params['ORDER_BY'])) $sql .= ' ORDER BY ' . $this->params['ORDER_BY'];

            if (isset($this->params['GROUP_BY'])) $sql .= ' GROUP BY ' . $this->params['GROUP_BY'];

            if (isset($this->params['HAVING'])) $sql .= ' HAVING ' . $this->params['HAVING'];

            return $sql;
        }
    }
}
