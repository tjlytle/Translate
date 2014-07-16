<?php
/**
 * @author Tim Lytle <tim@timlytle.net>
 */

namespace Translate\Service\Translate\Lingo;
use GuzzleHttp\Message\ResponseInterface;
use Translate\Service\Translate\TranslationInterface;

class Translation implements TranslationInterface
{
    protected $translation;

    public function __construct(ResponseInterface $response)
    {
        $data = $response->json();
        if($data['success'] != 'true'){
            throw new \RuntimeException('API response not successful');
        }

        $this->translation = $data['translation'];
    }

    public function getTranslation ()
    {
        return $this->translation;
    }
}