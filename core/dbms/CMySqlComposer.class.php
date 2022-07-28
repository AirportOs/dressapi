<?php
/**
 * 
 * DressAPI
 * @version 2.0 alpha
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @copyright Tufano Pasquale
 * @url https://dressapi.com
 * 
 * 
 * Compose a SQL query in dialectic MySql 
 * 
 */
namespace DressApi\core\dbms
{
    /**
     * Class MySqlComposer
     *
     * @package DressApi\core\dbms\
     */
    class CMySqlComposer extends CSqlComposerBase
    {

        /**
         * SQL query composer according to the MySQL standard
         *
         * @return string the composed SQL code
         */
        public function prepare(): string
        {
            // print_r($this->params);
            $sql = 'SELECT ' . $this->params['ITEMS'];

            // https://stackoverflow.com/questions/18581527/convert-query-from-mysql-to-oracle-using-match-against
            //
            // MySQL
            // SELECT table_id,
            // MATCH(title,body) AGAINST('grand hotel') AS score,
            // MATCH(title) AGAINST('grand') AS score0,
            // MATCH(title) AGAINST('hotel') AS score1
            // FROM tbl
            // WHERE MATCH(title,body) AGAINST('grand hotel')
            // ORDER BY score ASC

            // MySQL
            // SELECT table_id, MATCH (title, body) AGAINST ('english') AS score
            // FROM pictures
            // WHERE MATCH (title, body) AGAINST ('english')
            // ORDER BY score DESC;

            $score = 1;
            $match_where = '';
            $match_order = '';
            if (isset($this->params['MATCH']))
                foreach ($this->params['MATCH'] as $items => $text_to_search)
                {
                    $m = "MATCH($items) AGAINST(" . str_replace("'", "''", $text_to_search) . ") as score$score";
                    $sql .= ",$m";
                    $match_where .= (($match_where !== '') ? (' OR ') : ('')) . "$m";
                    $match_order .= (($match_order !== '') ? (', ') : ('')) . "score$score DESC";

                    $score++;
                }

            $sql .= ' FROM ' . trim(implode(',', $this->params['TABLE']));

            // JOINs
            foreach (['LEFT JOIN', 'JOIN', 'OUTER JOIN', 'INNER JOIN', 'RIGHT JOIN'] as $join_type)
                if (!empty($this->params[$join_type]) && !empty($this->params['ON']))
                    foreach ($this->params[$join_type] as $i => $join_table)
                        $sql .= " $join_type $join_table ON " . $this->params['ON'][$i];

            // WHERE
            if (!empty($this->params['WHERE']) || $match_where != '')
                $sql .= ' WHERE';

            if (!empty($this->params['WHERE']))
                $sql .= ' (' . $this->params['WHERE'] . ')';

            if (!empty($this->params['WHERE']) && $match_where != '')
                $sql .= ' AND ';

            if ($match_where != '')
                $sql .= ' (' . $match_where . ')';


            // ORDER BY
            if (!empty($this->params['ORDER_BY']) || $match_order != '')
                $sql .= ' ORDER BY ';

            if (!empty($this->params['ORDER_BY']))
                $sql .= ' ' . $this->params['ORDER_BY'];

            if (!empty($this->params['ORDER_BY']) && $match_order != '')
                $sql .= ', ';

            if ($match_order != '')
                $sql .= ' ' . $match_order;


            // GROUP BY
            if (!empty($this->params['GROUP_BY']))
                $sql .= ' GROUP BY ' . $this->params['GROUP_BY'];

            if (!empty($this->params['HAVING']))
                $sql .= ' HAVING ' . $this->params['HAVING'];

            if ($this->page !== 0)
                $sql .= ' LIMIT ' . (($this->page - 1) * $this->elements_per_page) . ',' . $this->elements_per_page;

            return $sql;
        }
    }
}
