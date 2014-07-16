<?php
/**
 * @author Tim Lytle <tim@timlytle.net>
 */

namespace Translate;
use Pimple\Container;
use Translate\Service\Nexmo\Nexmo;
use Translate\Service\Storage\Mongo;

require_once __DIR__ . '/vendor/autoload.php';

$pimple = new Container();

$pimple['nexmo'] = function(Container $c){
    return new Nexmo($c['nexmo.key'], $c['nexmo.secret']);
};

$pimple['translate'] = function(Container $c){
    return new Service\Translate\Lingo\Service($c['lingo.key']);
};

$pimple['storage'] = function(Container $c){
    $dsn = $c['mongo.dsn'];
    $client = new \MongoClient($dsn);

    $parts = explode('/', $dsn);
    $name = end($parts);

    $db = $client->selectDB($name);
    return new Mongo($db);
};

$config = new Config();
return $config($pimple);

