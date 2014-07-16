<?php
/**
 * @author Tim Lytle <tim@timlytle.net>
 */
use Respect\Rest\Router;

//everything is json
header('Content-Type: application/json');

$pimple = require __DIR__ . '/../bootstrap.php';
$r3 = new Router('/api.php');

/**
 * Get the languages we can translate from.
 */
$r3->get('/lang/source', function() use($pimple) {
    /* @var $service Translate\Service\Translate\TranslateInterface */
    $service = $pimple['translate'];
    return json_encode($service->getSources());
});

/**
 * Get the languages we can translate to, using a selected language.
 */
$r3->get('/lang/target/*', function($source) use($pimple) {
    /* @var $service Translate\Service\Translate\TranslateInterface */
    $service = $pimple['translate'];
    return json_encode($service->getTargets($source));
})->when(function($source) {
    return is_string($source);
});

/**
 * Get the source target and the best inbound number for a phone number.
 */
$getNumber = $r3->get('/number/*', function($number) use($pimple) {
    /* @var $storage Translate\Service\Storage\StorageInterface */
    $storage = $pimple['storage'];
    $settings = $storage->get($number);
    if(!$settings){
        return "{}";
    }

    /* @var $nexmo Translate\Service\Nexmo\Nexmo */
    $nexmo = $pimple['nexmo'];
    $inbound = $nexmo->getInboundNumber($number);
    $lang    = $nexmo->getVoiceLang($settings['target']);

    return json_encode(array_merge($settings, [
        'inbound' => $inbound,
        'voice'   => !empty($lang)
    ]));
});

/**
 * Associate a source and target language with a number.
 */
$r3->post('/number/*', function($number) use($pimple, $getNumber) {
    /* @var $storage Translate\Service\Storage\StorageInterface */
    $storage = $pimple['storage'];
    $storage->set($number, $_POST['source'], $_POST['target']);
    return $getNumber;
})->when(function($number){
    return (
        is_string($number) AND
        isset($_POST['source']) AND
        isset($_POST['target'])
    );
});


