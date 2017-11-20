<?php

namespace Mr\Sdk\Model\Account;


use Mr\Sdk\Model\BaseModel;

class User extends BaseModel
{
    public static function getResource()
    {
        return 'user';
    }
}