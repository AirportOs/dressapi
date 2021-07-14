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
 * Class for managing the cache with Redis
 * 
 */

namespace DressApi\Core\Cache;

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/redis-config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Exception;
use Predis\Collection\Iterator;

/**
 * Class CRedisCache
 * 
 * composer require predis/predis
 * 
 * Install redis server:
 *  on docker
 *    - docker pull redis
 *
 *  on Debian Linux (https://linuxize.com/post/how-to-install-and-configure-redis-on-debian-9/)
 *    - apt install redis-server
 *    - systemctl status redis-server
 *
 * Set Password:
 *  $>redis-cli
 *  Redis>config set requirepass password123
 *  Redis>auth password123
 *  Redis>exit
 * 
 * @package DressApi\Cache
 */
class CRedisCache
{
    private string $CACHE_PATH;
    private \Predis\Client $redis;
    private $area_name;
    /**
     * CFileCache constructor
     *
     * @param string $domain application domain
     */
    public function __construct(string $domain)
    {
        \Predis\Autoloader::register();

        $this->redis = new \Predis\Client(
            [
                "scheme"   => REDIS_SCHEME,
                "host"     => REDIS_HOST,
                "port"     => REDIS_PORT,
                "username" => REDIS_USERNAME,
                "password" => REDIS_PASSWORD
            ]
        );
        if (!$this->redis->isConnected())
            throw new Exception('The connection to redis cache is failed');

        // Set a unique prefix for the application
        $this->CACHE_PATH = $domain;
        $this->area_name = '';

        //  $this->redis->setOption(Redis::OPT_PREFIX, $this->CACHE_PATH);
    }


    /**
     * setArea
     *
     * Set the name of current area
     * 
     * @param string $area_name name of area 
     * 
     * @return void
     */
    public function setArea(string $area_name): void
    {
        $this->area_name = $area_name;
    }


    /**
     * clearArea
     *
     * remove all items of an area
     * 
     * @param string $area_name name of area 
     * 
     * @return void
     */
    public function clearArea(string $area_name = null): void
    {
        if ($area_name != null)
        {
            $tmp = $this->area_name;
            $this->setArea($area_name);
        }
        $this->clear('*');
        if ($area_name != null)
            $this->area_name = $tmp;
    }


    /**
     * getCachePath
     *
     * Returns the path where it writes the cache files
     *
     * @return string the cache path of the application
     */
    public function getCachePath(): string
    {
        return $this->CACHE_PATH;
    }

    
    /**
     * writeDebug
     *
     * Writes a message to a cache-specific log file
     *
     * @param string $s message to write on file log
     *
     * @return void
     */
    public function writeDebug(string $s): void
    {
        $path = realpath(__DIR__ . '/../../');
        $filename = $path . '/logs/dressapi-cache.log';
        $datarow =  date('Y-m-d H:i:s') . ' - ' . ((is_array($s)) ? (print_r($s, true)) : ($s)) . "\r\n";

        file_put_contents($filename, $datarow, LOCK_EX | FILE_APPEND);
    }


    /**
     * get
     *
     * Writes a message to a cache-specific log file
     *
     * @param string $s message to write on file log
     *
     * @return mixed The cached item: it can be a scalar value, an object or an array
     */
    public function get(string $name): mixed
    {
        $ret = null;
        if ($this->redis)
        {
            try
            {
                $value = $this->redis->get($this->getName($name));
                if ($value)
                    $ret = unserialize($value);
            }
            catch (\Predis\Connection\ConnectionException $e)
            {
                $ret = null; // die("Connessione a Redis non riuscita!");
            }
        }

        return $ret;
    }


    /**
     * exists method
     *
     * Check if a key exists and is in the cache
     *
     * @param string $name message to write on file log
     *
     * @return bool true if the key $name exists
     */
    public function exists(string $name): bool
    {
        return ($this->redis && $this->redis->get($this->getName($name)) != null);
    }


    /**
     * getName method
     *
     * Given the name of a key determines the actual internal key to be used
     *
     * @param string $name message to write on file log
     *
     * @return string the internal key
     */
    public function getName(string $name): string
    {
        return $this->CACHE_PATH . (($this->area_name != '') ? (':' . $this->area_name) : ('')) . ':' . $name;
    }


    /**
     * set Method
     * 
     * Sets the value of an item to be stored
     *
     * @param string $name key of the item to be stored
     * @param mixed $value value to be stored
     */
    public function set(string $name, mixed $value): void
    {
        $redisname = $this->getName($name);
        if ($this->redis)
            $this->redis->set($redisname, serialize($value)); // base64_encode(serialize($value))
    }


    /**
     * delete Method
     * 
     * Delete a certain key if it exists
     *
     * @param string $key name of the key to be deleted
     */
    public function delete(string $key): void
    {
        $realname = $this->getName($key);

        if ($this->redis)
            $this->redis->del($realname);
    }


    /**
     * clear Method
     * 
     * Reset all cache
     * 
     * @param $needle Removes all keys containing the key name, if $needle is empty string than remove all
     * 
     */
    public function clear(string $needle = ''): void
    {
        if ($this->redis->isConnected())
        {
            if ($needle === '')
                $this->redis->flushDb();
            else
            {
                $pattern = $this->CACHE_PATH . ':' . $this->area_name . ':' . $needle;
                foreach ($this->redis->keys($pattern) as $key)
                    $this->redis->del($key);
            }
        }
    }


    /**
     * getCacheNames Method
     * 
     * Provides the list of cached file names
     * 
     * @return an array with a list of cached file name or false if there are none
     * 
     */
    /**
     * Fornisce la lista dei nomi in cache
     */
    public function getCacheNames(): array|false
    {
        $pattern = '*test1';

        if ($this->redis)
            return $this->redis->keys("*");
        else
            return null;
    }
}
