<?php

namespace Mr\Sdk\Model\Account;


use Mr\Bootstrap\Model\BaseModel;

class OAuthToken extends BaseModel
{
    public static function getResource()
    {
        return 'oauth_token';
    }
}