<?php

namespace Mr\Sdk\Service;

use Mr\Sdk\Model\Account\User;
use Mr\Sdk\Repository\Account\UserRepository;

class AccountService extends BaseService
{
    const BASE_URL = 'http://localhost:8002/api/v1/';

    protected function getBaseUrl()
    {
        return self::BASE_URL;
    }

    /**
     * Returns user by id
     *
     * @param $id
     * @return mixed
     */
    public function getUser($id)
    {
        return $this->_get(UserRepository::class)->get($id);
    }

    /**
     * Returns all users matching filters
     *
     * @param array $filters
     * @return array
     */
    public function findUsers(array $filters = [])
    {
        return $this->_get(UserRepository::class)->find($filters);
    }

    /**
     * Returns first user matching filters
     *
     * @param array $filters
     * @return mixed
     */
    public function findOneUser(array $filters = [])
    {
        return $this->_get(UserRepository::class)->findOne($filters);
    }

    /**
     * Create new user instance. Does not persist user.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data = [])
    {
        return $this->_get(UserRepository::class)->create($data);
    }
}