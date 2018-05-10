<?php

namespace Mr\Sdk\Model\Account;


use Mr\Bootstrap\Model\BaseModel;

class User extends BaseModel
{
    public static function getResource()
    {
        return 'user';
    }
}