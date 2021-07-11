<?php
/**
 * 
 * DressAPI
 * @version 1.0
 * @author Tufano Pasquale
 * @date 2020-2021
 * 
 * @This file is under Apache 2.0 license
 * 
 * 
 * Base class for connection and management of the MySql db
 * 
 */


spl_autoload_register(function ($class) {
    require_once str_replace('\\','/',__DIR__.'/../'.substr($class,strpos($class,'\\')+1)).'.class.php'; // Compositore di query
});