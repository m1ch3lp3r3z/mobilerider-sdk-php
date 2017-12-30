<?php

namespace Mr\Sdk\Model\Account;


use Mr\Sdk\Model\BaseModel;

class OAuthToken extends BaseModel
{
    public static function getResource()
    {
        return 'oauth_token';
    }
}