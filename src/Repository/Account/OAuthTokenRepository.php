<?php


namespace Mr\Sdk\Repository\Account;


use Mr\Bootstrap\Repository\BaseRepository;
use Mr\Sdk\Model\Account\OAuthToken;

class OAuthTokenRepository extends BaseRepository
{
    public function getModelClass()
    {
        return OAuthToken::class;
    }

    public function getByProvider($provider, $liveMode = true)
    {
        $data = $this->client->getData(
            $this->getUri(null, ['provider', $provider]),
            ['live_mode' => intval($liveMode)]
        );

        if (! $data) {
            return null;
        }

        $data = $this->parseMany($data);

        $data = $data[0];

        return $this->create($data);
    }
}