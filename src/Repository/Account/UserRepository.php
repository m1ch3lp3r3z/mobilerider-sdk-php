<?php


namespace Mr\Sdk\Repository\Account;


use Mr\Bootstrap\Repository\BaseRepository;
use Mr\Sdk\Model\Account\User;

class UserRepository extends BaseRepository
{
    public function getModelClass()
    {
        return User::class;
    }
}