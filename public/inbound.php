<?php
/**
 * @author Tim Lytle <tim@timlytle.net>
 */

use Translate\Service\Translate\TranslationInterface;

$request = array_merge($_POST, $_GET);

if(!isset($request['to']) OR !isset($request['msisdn']) OR !isset($request['text'])){
    error_log('not inbound message');
    return;
}

$pimple = require __DIR__ . '/../bootstrap.php';
/* @var $storage Translate\Service\Storage\StorageInterface */
$storage = $pimple['storage'];

//default text
$text = 'Setup SMS translation for your phone: http://translate.nexmodemo.com';

$settings = $storage->get($request['msisdn']);

if($settings){
    /* @var $service Translate\Service\Translate\TranslateInterface */
    $service = $pimple['translate'];

    if($storage->count($request['msisdn'], new DateTime()) > 3){
        $text = 'Sorry, only 3 requests per day.';
        error_log('hit max requests');
    } else {
        try{
            $result = $service->requestTranslation($request['text'], $settings['source'], $settings['target']);
            if($result instanceof TranslationInterface){
                $text = $result->getTranslation();
                $storage->log($request['msisdn'], $request['text'], $text);
            } else {
                throw new UnexpectedValueException('unknown translation type');
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $text = 'Sorry, did not catch that, can you send it again?';
        }
    }
}

/* @var $service Translate\Service\Nexmo\Nexmo */
$nexmo = $pimple['nexmo'];
error_log('sending message [' . $request['msisdn'] . ']:' . $text);
$nexmo->send($text, $request['msisdn'], $request['to']); //reply to the message
