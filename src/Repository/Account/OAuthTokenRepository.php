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

    public function getByProvider($provider, $asArray = false)
    {
        return $this->one([['provider', $provider]], $asArray);
    }
}