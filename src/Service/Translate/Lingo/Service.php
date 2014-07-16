<?php
/**
 * @author Tim Lytle <tim@timlytle.net>
 */
namespace Translate\Service\Translate\Lingo;
use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface;
use Translate\Http\HasClientInterface;
use Translate\Http\HasClientTrait;
use Translate\Service\Translate\TranslateInterface;

class Service implements TranslateInterface, HasClientInterface
{
    use HasClientTrait;

    protected $key;

    public function __construct($key, $base = 'https://api.lingo24.com')
    {
        $this->key = $key;

        $client = new Client([
            'base_url' => $base,
            'defaults' => [
                'verify' => false
            ]
        ]);

        $this->setHttpClient($client);
    }

    public function requestTranslation ($text, $source, $target, $path = '/mt/v1/translate')
    {
        $request = $this->getRequest('POST');

        $request->setPath($path);
        $request->getBody()->setField('q', $text)
                           ->setField('source', $source)
                           ->setField('target', $target);

        $response = $this->processResponse($this->client->send($request));

        return new Translation($response);
    }

    public function getTargets ($source = null, $path = '/mt/v1/targetlangs')
    {
        $request = $this->getRequest('GET');
        $request->setPath($path);

        if($source){
            $request->getQuery()->add('source', $source);
        }

        $response = $this->processResponse($this->client->send($request));

        return $this->filterLangs($response->json()['target_langs']);
    }

    public function getSources ($target = null, $path = '/mt/v1/sourcelangs')
    {
        $request = $this->getRequest('GET');
        $request->setPath($path);

        if($target){
            $request->getQuery()->add('target', $target);
        }

        $response = $this->processResponse($this->client->send($request));

        return $this->filterLangs($response->json()['source_langs']);
    }

    protected function filterLangs(array $json)
    {
        $langs = [];
        foreach($json as $data){
            $langs[$data[0]] = $data[1];
        }
        return $langs;
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function processResponse(ResponseInterface $response)
    {
        $data = $response->json();
        if($data['success'] == 'false'){
            throw new \RuntimeException(implode(', ', $data['errors']));
        }

        return $response;
    }

    /**
     * @param $method
     * @return \GuzzleHttp\Message\RequestInterface
     */
    protected function getRequest($method)
    {
        $request = $this->getHttpClient()->createRequest($method);
        switch($method){
            case 'GET':
                $request->getQuery()->add('user_key', $this->key);
                break;
            case 'POST':
                $request->getBody()->setField('user_key', $this->key);
        }

        return $request;
    }
}