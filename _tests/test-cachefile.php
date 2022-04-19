<?php
    define('DOMAIN_NAME','dressapi.com');

    require_once '../../Core/autoload.php';

    use DressApi\Core\Cache\CFileCache as CCache;

    $obj = new stdClass();
    $obj->value = 45;
    $obj->svalue = 'Hello';

    $cache = new CCache(DOMAIN_NAME);
    $cache->setArea('MyArea1');
    $cache->set('test1', [1,2,3,4]);
    $cache->set('test2', $obj);

    echo "<pre>";

    $cache->setArea('MyArea2');
    $cache->set('test3', [11,31]);
    
    echo "\nALL CACHE NAMES OF MyArea2:\n";
    print_r($cache->getCacheNames());
    
    echo "\nVALUE of MyArea2::test3\n";
    print_r($cache->get('test3'));


    echo "\nDELETE ALL CONTENTS OF MyArea1";
    print_r($cache->clearArea('MyArea1'));
    
    echo "\nGET ALL CACHE NAMES OF CURRENT AREA (is MyArea2)";
    print_r($cache->getCacheNames());

    echo "\nVALUE of MyArea1::test1 (It no longer exists)\n";
    var_dump($cache->get('test1'));

    // delete all file of Cache
    $cache->clear();
