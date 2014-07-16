<?php
/**
 * @author Tim Lytle <tim@timlytle.net>
 */

namespace Translate\Service\Nexmo;
use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface;
use Translate\Http\HasClientInterface;
use Translate\Http\HasClientTrait;

class Nexmo implements HasClientInterface
{
    use HasClientTrait;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $secret;

    public function __construct($key, $secret, $base = 'https://rest.nexmo.com')
    {
        $this->key    = $key;
        $this->secret = $secret;

        $client = new Client([
            'base_url' => $base
        ]);

        $this->setHttpClient($client);
    }

    public function send($text, $to, $from, $path = '/sms/json')
    {
        $request = $this->getRequest($path);
        $request->getBody()->setField('text', $text)
                           ->setField('to',   $to)
                           ->setField('from', $from);

        $response = $this->processResponse($this->getHttpClient()->send($request));
        return $response->json();
    }

    public function getVoiceLang($lang)
    {
        switch($lang){
            case 'en':
                return 'en-gb';
            case 'es-419':
                return 'es-mx';
            case 'fr':
            case 'de':
            case 'es':
            case 'it':
            case 'nl':
            case 'pl':
            case 'pt':
            case 'ru':
                return "$lang-$lang";
            case 'ja':
                return 'ja-jp';
            case 'zh':
            case 'zh-tw':
                return 'zh-cn';
            default:
                return;
        }
    }

    public function getInboundNumber($number)
    {
        return '15637944044';
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function processResponse(ResponseInterface $response)
    {
        $data = $response->json();
        foreach($data['messages'] as $message){
            if($message['status'] != 0){
                throw new \RuntimeException('api error: ' . $message['error_text']);
            }
        }

        return $response;
    }

    protected function getRequest($path)
    {
        $request = $this->getHttpClient()->createRequest('POST');
        $request->setPath($path);
        $request->getBody()->setField('api_key', $this->key)
                           ->setField('api_secret', $this->secret);

        return $request;
    }
} 