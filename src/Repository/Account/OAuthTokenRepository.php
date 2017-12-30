<?php


namespace Mr\Sdk\Repository\Account;


use Mr\Sdk\Model\Account\OAuthToken;
use Mr\Sdk\Repository\BaseRepository;

class OAuthTokenRepository extends BaseRepository
{
    public static function getModelClass()
    {
        return OAuthToken::class;
    }

    public function getByProvider($provider, $asArray = false)
    {
        $data = $this->client->getData($this->getUri(null, ['provider', $provider]));

        $data = $this->parseOne($data);

        return $asArray ? $data : $this->create($data);
    }
}