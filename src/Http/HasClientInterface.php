<?php
/**
 * @author Tim Lytle <tim@timlytle.net>
 */

namespace Translate\Http;
use GuzzleHttp\Client;
interface HasClientInterface
{
    public function setHttpClient(Client $client);

    /**
     * @return Client
     */
    public function getHttpClient();
}