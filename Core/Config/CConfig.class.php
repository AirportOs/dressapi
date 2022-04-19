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
 * User authentication and authorization
 *  
 */

namespace DressApi\Core\Config;

use Exception;
use Firebase\JWT\JWT;
use DressApi\Core\DBMS\CMySqlDB as CDB;
use DressApi\Core\DBMS\CMySqlComposer as CSqlComposer;
use DressApi\Core\Cache\CFileCache as CCache; // An alternative is CRedisCache


class CConfig extends CDB
{
    protected ?CCache $cache;
    protected array $values = [];
    
    /**
     * Constructor
     */
    public function __construct(?CCache $cache) 
    {
        $this->cache = $cache;

        $sc = new CSqlComposer();
        $sql = $sc->select('name,val')->from(CONFIG_TABLE);

        if ($this->cache && $this->cache->exists('values','configurations'))
            $this->values = $this->cache->get('values','configurations');
        else
        {
            $this->values = [];
            $this->getQueryDataTable($this->values, $sql, null, null, self::DB_ASSOC, 'name');
            if ($this->cache!==null && $this->values !== null)     
                $this->cache->set('values', $this->values, 'configurations');           
        }
        
    }

    /**
     * Get a value from config table
     * 
     * @param string $name name of value
     * 
     * @return string the value associated to the name or '' if not exist
     */
    public function get(string $name) : string
    {
        $ret = '';
        if (isset($this->values[$name]))
            $ret = $this->values[$name];

        return $ret;
    }
}
