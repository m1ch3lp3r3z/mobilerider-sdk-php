<?php


namespace Mr\Sdk\Repository\Account;


use Mr\Sdk\Model\Account\User;
use Mr\Sdk\Repository\BaseRepository;

class UserRepository extends BaseRepository
{
    public static function getModelClass()
    {
        return User::class;
    }
}