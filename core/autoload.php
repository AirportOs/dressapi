<?php
/**
 * 
 * DressAPI
 * @version 1.0
 * @author Tufano Pasquale
 * @copyright Tufano Pasquale
 * @date 2020-2022
 * 
 * @This file is under Apache 2.0 license
 * 
 * 
 * Base class for connection and management of the MySql db
 * 
 */


spl_autoload_register(function ($class) {
    $required_file = str_replace('\\','/',__DIR__.'/../'.substr($class,strpos($class,'\\')+1)).'.class.php';
    require_once $required_file; // Compositore di query
});