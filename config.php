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
        $container['lingo.key'] = getenv('LINGO_KEY');
        $container['mongo.dsn'] = getenv('MONGO_DSN');
        $container['nexmo.key'] = getenv('NEXMO_KEY');
        $container['nexmo.secret'] = getenv('NEXMO_SECRET');

        return $container;
    }
}