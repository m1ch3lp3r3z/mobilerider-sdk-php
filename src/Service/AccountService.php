<?php

namespace Mr\Sdk\Service;

use Mr\Sdk\Model\Account\OAuthToken;
use Mr\Sdk\Model\Account\User;
use Mr\Sdk\Model\Account\Vendor;
use Mr\Sdk\Repository\Account\OAuthTokenRepository;
use Mr\Sdk\Repository\Account\UserRepository;
use Mr\Sdk\Repository\Account\VendorRepository;

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
     * @return User
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
     * @return User
     */
    public function findOneUser(array $filters = [])
    {
        return $this->_get(UserRepository::class)->findOne($filters);
    }

    /**
     * Returns user by id
     *
     * @param $id
     * @return Vendor
     */
    public function getVendor($id)
    {
        return $this->_get(VendorRepository::class)->get($id);
    }

    /**
     * Returns all vendors matching filters
     *
     * @param array $filters
     * @return array
     */
    public function findVendors(array $filters = [])
    {
        return $this->_get(VendorRepository::class)->find($filters);
    }

    /**
     * Returns first vendor matching filters
     *
     * @param array $filters
     * @return Vendor
     */
    public function findOneVendor(array $filters = [])
    {
        return $this->_get(VendorRepository::class)->findOne($filters);
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

    /**
     * @param array $data
     * @return Vendor
     */
    public function createVendor(array $data = [])
    {
        return $this->_get(VendorRepository::class)->create($data);
    }

    public function getOAuthToken($id)
    {
        return $this->_get(UserRepository::class)->get($id);
    }

    /**
     * @param $provider
     * @return OAuthToken
     */
    public function getOAuthTokenByProvider($provider)
    {
        return $this->_get(OAuthTokenRepository::class)->getByProvider($provider);
    }
}