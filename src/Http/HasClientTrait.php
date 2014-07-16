<?php
/**
 * @author Tim Lytle <tim@timlytle.net>
 */
namespace Translate\Http;
use GuzzleHttp\Client;
trait HasClientTrait
{
    /**
     * @var Client
     */
    protected $client;

    public function setHttpClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        if(empty($this->client)){
            $this->setHttpClient(new Client());
        }

        return $this->client;
    }
}