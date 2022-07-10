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
 * @date 2020-2022
 * 
 * Class for managing the cache with files
 * 
 */

namespace DressApi\core\cache;

require_once __DIR__ . '/config.php';

use Exception;
use DressApi\core\cache\ICache;

/**
 * Class CCache
 *
 * @package DressApi\core\cache
 */
class CFileCache implements ICache
{
    private const MAIN_CACHE_PATH = CACHE_PATH; 
    private string $CACHE_PATH;
    private string $area_name;
    private int $uid; // _user::id__user

    /**
     * CFileCache constructor
     *
     * @param string $domain application domain (unique name for each app)
     * @param string $db_name name of db
     * @param string $area_name name of area 
     * @param int $uid the id of current user in table _user (optional)
     */
    public function __construct(string $domain, string $db_name, string $area_name = '', int $uid = 0)
    {
        // Create the cachefiles folder in the given path. Useful for example if it writes to memory like '/dev/shm/'
        $this->CACHE_PATH = self::MAIN_CACHE_PATH . $domain . '/'.$db_name.'/';
        $this->area_name = $area_name;
        $this->uid = $uid;
        $this->_createPathIfNotExists($this->CACHE_PATH);

        //    print $this->CACHE_PATH;
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
    public function clearArea(string $area_name): void
    {
        if ($area_name != null)
        {
            $tmp = $this->area_name;
            $this->setArea($area_name);
        }
        $this->clear();
        if ($area_name != null)
            $this->area_name = $tmp;
    }


    /**
     * setUid
     *
     * Set the id of current user
     * 
     * @param int $uid id of current user (optional)
     * 
     * @return void
     */
    public function setUid(int $uid): void
    {
        $this->uid = $uid;
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
        return $this->CACHE_PATH . 
               (($this->area_name != '') ? ($this->area_name . '/') : ('')) . 
               (($this->uid != 0) ? ($this->uid) : ('global')) . '.' . 
               $name . 
               '.cache';
    }


    /**
     * get Method
     * 
     * Get the value of an stored element
     *
     * @param string $name key of the item to be stored
     * @param string $area_name name of area (null or not declared is the implicit "current area") 
     *
     * @return mixed The cached element value: it can be a scalar value, an object or an array
     */
    public function get(string $name, ?string $area_name = null): mixed
    {
        $ret = null;
        if ($area_name!==null)
            $this->setArea($area_name);

        if ($this->exists($name))
        {
            try
            {
                $value = file_get_contents($this->getName($name));
                $ret = unserialize($value);
            }
            catch (Exception $ex)
            {
                $ret = null;
                $this->WriteDebug('ERROR ON CACHE: ' . $value . ' ' . $ex->getMessage());
            }
        }

        return $ret;
    }


    /**
     * getGlobal Method
     * 
     * Get the value of an stored element valid for all users
     *
     * @param string $name key of the item to be stored
     * @param string $area_name name of area (null or not declared is the implicit "current area") 
     *
     * @return mixed The cached element value: it can be a scalar value, an object or an array
     */
    public function getGlobal(string $name, ?string $area_name = null): mixed
    {
        $uid = $this->uid;
        $this->uid = 0;
        $ret = $this->get($name, $area_name);
        $this->uid = $uid;

        return $ret;
    }


    /**
     * exists method
     *
     * Check if a key exists and is in the cache
     *
     * @param string $name message to write on file log
     * @param string $area_name name of area (null or not declared is the implicit "current area") 
     *
     * @return bool true if the key $name exists
     */
    public function exists(string $name, ?string $area_name = null): bool
    {
        if ($area_name!==null)
            $this->setArea($area_name);
        
        return (file_exists($this->getName($name)));
    }


    /**
     * existsGlobal method
     *
     * Check if a key exists for all user and is in the cache
     *
     * @param string $name key of the item to be stored
     * @param string $area_name name of area (null or not declared is the implicit "current area") 
     *
     * @return bool true if the key $name exists
     */
    public function existsGlobal(string $name, ?string $area_name = null): bool
    {
        $uid = $this->uid;
        $this->uid = 0;
        $ret = $this->exists($name, $area_name);
        $this->uid = $uid;

        return $ret;
    }


    /**
     * set Method
     * 
     * Sets the value of an item to be stored
     *
     * @param string $name key of the item to be stored
     * @param mixed $value value to be stored
     * @param string $area_name name of area (null or not declared is the implicit "current area")
     *  
     */
    public function set(string $name, mixed $value, ?string $area_name = null): void
    {
        if ($area_name!==null)
            $this->setArea($area_name);
        
        $filename = $this->getName($name);
        if (strstr($filename, '/'))
        {
            $cache_path = dirname($filename);
            $this->_createPathIfNotExists($cache_path);
        }

        $serialized = serialize($value);
        if (strlen($serialized) <= CACHE_MAX_SIZE_PER_ELEMENT) // save only if it is below the maximum allowed size 
            file_put_contents($filename, $serialized, LOCK_EX);
    }


    /**
     * setGlobal Method
     * 
     * Sets the value of an item to be stored valid for all user
     *
     * @param string $name key of the item to be stored
     * @param mixed $value value to be stored
     * @param string $area_name name of area (null or not declared is the implicit "current area")
     *  
     */
    public function setGlobal(string $name, mixed $value, ?string $area_name = null): void
    {
        $uid = $this->uid;
        $this->uid = 0;
        $this->set($name, $value, $area_name);
        $this->uid = $uid;
    }


    /**
     * Delete a certain key if it exists
     *
     * @param string $name name of the key to be deleted
     */
    public function delete(string $name): void
    {
        $filename = $this->getName($name);

        if ($this->exists($filename))
            unlink($filename);
    }


    /**
     * clear Method
     * 
     * Reset all cache
     * 
     * @param $needle Removes all keys containing the key name, if $needle is empty string than remove all
     * 
     */
    public function clear(string $needle = '*')
    {
        // Linux, BSD, Solaris, etc.
        if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN')
        {
            $cmd = 'rm -R ' . $this->CACHE_PATH . (($this->area_name != '') ? ($this->area_name . '/') : (''));
            if ($needle !== NULL)
                $cmd .= $needle;

            shell_exec($cmd);
        }
        else // WINDOWS
        {
            $all_file_to_delete = $this->getCacheNames();
            if ($all_file_to_delete)
                foreach ($all_file_to_delete as $df)
                    if ($df != '.' && $df != '..')
                    {
                        $f = $this->CACHE_PATH . (($this->area_name != '') ? ($this->area_name . '/') : ('')) . $df;
                        if (is_file($f))
                            unlink($f);
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
    public function getCacheNames(): array|false
    {
        $path =  $this->CACHE_PATH . (($this->area_name != '') ? ($this->area_name . '/') : (''));

        return ((is_dir($path)) ? (scandir($path)) : (false));
    }


    /**
     * Create all folder's Path on file system if not exist
     * 
     * @param string $path path to create
     * @param mixed $value value to be stored
     */
    private function _createPathIfNotExists(string $path)
    {
        if (!is_dir($path))
        {
            // $apache_user = getenv('APACHE_RUN_USER');
            // if (!$apache_user)
            //    $apache_user = posix_getuid();
            mkdir($path, 0700, true);
            // @chown($path, $apache_user);
            // @chgrp($path, $apache_user);

            // $this->WriteDebug("$zone $prefix_zone $domain");
            // $this->WriteDebug(debug_backtrace());
        }
    }
}
