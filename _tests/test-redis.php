<?php
    define('DOMAIN_NAME','dressapi.com');
    
    require_once __DIR__.'/../../Core/autoload.php';

    use DressApi\Core\Cache\CRedisCache as CCache;

    $obj = new stdClass();
    $obj->value = 45;
    $obj->svalue = 'ciao';

    try
    {
        $redis = new CCache(DOMAIN_NAME);
        $redis->clear();
        $redis->setArea('pippo');
        $redis->set('test1', [1,2,3,4]);
        $redis->set('test2', $obj);
    
        echo "<pre>";
    
        $redis->setArea('pluto');
        $redis->set('cap4', [11,21,31,41]);
        print_r($redis->getCacheNames());
    
        print_r($redis->clearArea('pluto'));
        print_r($redis->getCacheNames());
    
        var_dump($redis->get('cap4'));    
    }
    catch(Exception $ex)
    {
        print $ex->getMessage();
    }


    