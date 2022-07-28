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
 * User authentication and authorization 
 */

namespace DressApi\core\request;

use DressApi\core\dbms\CMySqlDB as CDB;
use DressApi\core\dbms\CMySqlComposer as CSqlComposer;
use Exception;

class CRoute extends CDB
{   
    protected array $routes = [];              // all routes

    /**
     * Initialize CRoute
     */    
    public function __construct()
    {
        $this->reset();
        $this->importFromDB();
    }


    /**
     * No routes exists after reset
     */    
    public function reset()
    {
        $this->routes = [];
    }


    /**
     * Add a route
     *
     * @param string $src_route the origin (source) path 
     * @param string $dst_route the destination path 
     * @param bool $force if is true (default value) force to set the current value with dst_source if already exists 
     * @throw in case $force is false and the source path already exists
     */    
    public function add( string $src_route, string $dst_route, $force = true )
    {
        if (!$force && isset($this->routes[$src_route]))
            new Exception('The source route already exists');

        $this->routes[$src_route] = $dst_route;
    }


    /**
     * Verify is the $route exists
     *
     * @param string $route the origin path to check 
     * @return bool true if exists 
     */    
    public function checkIfExists( string $route ) : bool
    {
        return (isset($this->routes[$route]));
    }

    
    /**
     * Return the destination path from origin path
     *
     * @param string $route the origin path 
     * @return ?string the value of destination path if exists, otherwise return null 
     */    
    public function get(string $route) : ?string
    {
        if (isset($this->routes[$route]))
            return $this->routes[$route];
            
        return null;
    } 


    /**
     * Change the origin path with the destination path if exists, otherwise it leaves the value unchanged
     *
     * @param string &$route the origin path to change 
     */    
    public function changeIfExists(string &$route)
    {
        if (isset($this->routes[$route]))
            $route = $this->routes[$route];
    }


    /**
     * Import all routes from db table
     */    
    protected function importFromDB()
    {
        $sc = new CSqlComposer();

        $sql = $sc->select('origin_path,destination_path')->from(ROUTE_TABLE);

        // echo "\n$sql\n";
        $this->query($sql);   
        $this->routes = $this->getArrayByName('origin_path','destination_path');
    }
}