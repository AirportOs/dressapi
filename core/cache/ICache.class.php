<?php

namespace DressApi\core\cache;


// Declare the interface 'Template'
interface ICache
{
    /**
     * setArea
     *
     * Set the name of current area
     * 
     * @param string $area_name name of area 
     * 
     * @return void
     */
    public function setArea(string $area_name): void;


    /**
     * clearArea
     *
     * remove all items of an area
     * 
     * @param string $area_name name of area 
     * 
     * @return void
     */
    public function clearArea(string $area_name): void;


    /**
     * setArea
     *
     * Set the name of current area
     * 
     * @param int $uid the id of current user in table _user (optional)
     * 
     * @return void
     */
    public function setUid(int $uid): void;


    /**
     * getCachePath
     *
     * Returns the path where it writes the cache files
     *
     * @return string the cache path of the application
     */
    public function getCachePath(): string;


    /**
     * writeDebug
     *
     * Writes a message to a cache-specific log file
     *
     * @param string $s message to write on file log
     *
     * @return void
     */
    public function writeDebug(string $s): void;


    /**
     * getName method
     *
     * Given the name of a key determines the actual internal key to be used
     *
     * @param string $name key of the item to be stored
     *
     * @return string the internal key
     */
    public function getName(string $name): string;



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
    public function get(string $name, ?string $area_name = null): mixed;


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
    public function getGlobal(string $name, ?string $area_name = null): mixed;

    
    /**
     * exists method
     *
     * Check if a key exists and is in the cache
     *
     * @param string $name key of the item to be stored
     * @param string $area_name name of area (null or not declared is the implicit "current area") 
     *
     * @return bool true if the key $name exists
     */
    public function exists(string $name, ?string $area_name = null): bool;


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
    public function existsGlobal(string $name, ?string $area_name = null): bool;


    /**
     * set Method
     * 
     * Sets the value of an element to be stored
     *
     * @param string $name key of the item to be stored
     * @param mixed $value value to be stored
     * @param string $area_name name of area (null or not declared is the implicit "current area")
     *  
     */
    public function set(string $name, mixed $value, ?string $area_name = null): void;


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
    public function setGlobal(string $name, mixed $value, ?string $area_name = null): void;


    /**
     * Delete a certain key if it exists
     *
     * @param string $name name of the key to be deleted
     */
    public function delete(string $name): void;


    /**
     * clear Method
     * 
     * Reset all cache
     * 
     * @param $needle Removes all keys containing the key name, if $needle is empty string than remove all
     * 
     */
    public function clear(string $needle = '*');


    /**
     * getCacheNames Method
     * 
     * Provides the list of cached file names
     * 
     * @return an array with a list of cached file name or false if there are none
     * 
     */
    public function getCacheNames(): array|false;

}
