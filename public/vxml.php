<?php
$request = array_merge($_GET, $_POST);
echo '<?xml version="1.0" encoding="UTF-8"?>';
if(!isset($request['nexmo_caller_id'])){
    error_log('no caller id');
    return include __DIR__ . '/vxml/404.php';
}

$pimple = require __DIR__ . '/../bootstrap.php';
/* @var $storage Translate\Service\Storage\StorageInterface */
$storage = $pimple['storage'];
$last = $storage->last($request['nexmo_caller_id']);

if(!$last){
    error_log('no last message');
    return include __DIR__ . '/vxml/404.php';
}

/* @var $nexmo Translate\Service\Nexmo\Nexmo */
$nexmo = $pimple['nexmo'];
$lang    = $nexmo->getVoiceLang($last['settings']['target']);

if(!$lang){
    error_log('not a supported language: ' . $last['settings']['target']);
    return include __DIR__ . '/vxml/unsupported.php';
}

return include __DIR__ . '/vxml/supported.php';
