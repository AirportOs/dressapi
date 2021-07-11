<?php
/**
 * 
 * DressAPI
 * @version 1.0
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @url https://dressapi.com
 * 
 * @date 2020-2021
 * 
 * Class for managing the cache with files
 * 
 */

namespace DressApi\Core\Cache;

require_once __DIR__.'/config.php';

use Exception;

/**
 * Class CCache
 *
 * @package DressApi\Cache
 */
class CFileCache
{
  private const MAIN_CACHE_PATH = '/dev/shm/';
  private string $CACHE_PATH;
  private $area_name;

  /**
   * CFileCache constructor
   *
   * @param string $domain application domain (unique name for each app)
   */
  public function __construct(string $domain)
  {
    // Crea la cartella cachefiles nella path indicata. Utile ad esempio se scrive in memoria tipo '/dev/shm/'
    $this->CACHE_PATH = self::MAIN_CACHE_PATH . $domain . '/cachefiles/';
    $this->area_name = '';
    $this->_createPathIfNotExists( $this->CACHE_PATH = self::MAIN_CACHE_PATH . $domain . '/cachefiles/' );

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
  public function setArea(string $area_name) : void
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
  public function clearArea(string $area_name) : void
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
  public function writeDebug(string $s) : void
  {
    $f = fopen(__DIR__ . '/../../cache.log', 'ab');
    if ($f != null)
    {
      fwrite($f, date('Y-m-d H:i:s') . ' - ' . ((is_array($s)) ? (print_r($s, true)) : ($s)) . "\r\n");
      fclose($f);
    }
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
    if ($this->exists($name))
    {
      try
      {
        $value = file_get_contents($this->getName($name));
        $ret = unserialize($value);
      }
      catch ( Exception $ex )
      {
        $ret = null;
        $this->WriteDebug('ERROR ON CACHE: ' . $value . ' ' . $ex->getMessage());
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
    return (file_exists($this->getName($name)));
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
    return $this->CACHE_PATH .(($this->area_name!='')?($this->area_name. '/'):('')) . $name.'.cache';
  }

  /**
   * Create all folder's Path on file system if not exist
   * 
   * @param string $path path to create
   * @param mixed $value value to be stored
   */
  private function _createPathIfNotExists( string $path )
  {
    if (!is_dir($path))
    {
      // mkdir($this->CACHE_PATH,0777,true);
      mkdir($path, 0755, true);
      chown($path, 'apache');
      chgrp($path, 'apache');
      chown($path, 'www-data');
      chgrp($path, 'www-data');
      // $this->WriteDebug("$zone $prefix_zone $domain");
      // $this->WriteDebug(debug_backtrace());
    }
  }


  /**
   * Sets the value of an item to be stored
   *
   * @param string $name key of the item to be stored
   * @param mixed $value value to be stored
   */
  public function set(string $name, mixed $value) : void
  {
    $filename = $this->getName($name);
    if (strstr($filename, '/'))
    {
      $cache_path = dirname($filename);
      $this->_createPathIfNotExists( $cache_path );
    }

    $serialized = serialize($value);
    if (strlen($serialized)<=CACHE_MAX_SIZE_PER_ELEMENT) // save only if it is below the maximum allowed size 
      file_put_contents($filename, $serialized, LOCK_EX);
  }


  /**
   * Delete a certain key if it exists
   *
   * @param string $name name of the key to be deleted
   */
  public function delete(string $name) : void
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
    if (substr(PHP_OS,0,3)=='Linux')
    {
      $cmd = 'rm -R ' . $this->CACHE_PATH . (($this->area_name!='')?($this->area_name. '/'):(''));
      if ($needle !== NULL) 
        $cmd .= $needle;

      shell_exec($cmd);
    }

    // Per WINDOWS
    if (substr(PHP_OS,0,3)=='WIN')
    {
      $all_file_to_delete = $this->getCacheNames();
      if ($all_file_to_delete)
        foreach($all_file_to_delete as $df)
            if ($df!='.' && $df!='..')
            {
                $f = $this->CACHE_PATH.(($this->area_name!='')?($this->area_name. '/'):('')).$df;
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
    $path =  $this->CACHE_PATH .(($this->area_name!='')?($this->area_name. '/'):(''));
    
    return ( (is_dir($path)) ? (scandir($path)) : (false));
  }
}


