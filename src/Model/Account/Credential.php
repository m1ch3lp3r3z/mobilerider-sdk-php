<?php

namespace Mr\Sdk\Model\Account;


use Mr\Bootstrap\Model\BaseModel;

class Credential extends BaseModel
{
    public static function getResource()
    {
        return 'credential';
    }
}