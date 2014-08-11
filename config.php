<?php
/**
 * @author Tim Lytle <tim@timlytle.net>
 */

namespace Translate;
use Pimple\Container;

class Config
{
    public function __invoke(Container $container)
    {
        //config can be set from json file
        $json = __DIR__ . '/config.json';
        if(file_exists($json)){
            $config = json_decode(file_get_contents($json), true);
            foreach($this->reduce($config) as $key => $value){
                $container[$key] = $value;
            }
        //expect environment has them
        } else {
            $container['lingo.key'] = getenv('LINGO_KEY');
            $container['mongo.dsn'] = getenv('MONGO_DSN');
            $container['nexmo.key'] = getenv('NEXMO_KEY');
            $container['nexmo.secret'] = getenv('NEXMO_SECRET');
        }

        return $container;
    }

    protected function reduce(array $array, $prefix = '')
    {
        $return = array();

        foreach($array as $key => $value){
            if(is_array($value)){
                foreach($this->reduce($value, $key . '.') as $newKey => $newValue){
                    $return[$prefix . $newKey] = $newValue;
                }
            } else {
                $return[$prefix . $key] = $value;
            }
        }

        return $return;
    }
}