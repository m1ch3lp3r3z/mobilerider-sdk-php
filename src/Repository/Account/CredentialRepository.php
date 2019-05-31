<?php


namespace Mr\Sdk\Repository\Account;


use Mr\Bootstrap\Repository\BaseRepository;
use Mr\Sdk\Model\Account\Credential;

class CredentialRepository extends BaseRepository
{
    public function getModelClass()
    {
        return Credential::class;
    }
}