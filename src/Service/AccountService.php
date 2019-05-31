<?php

namespace Mr\Sdk\Service;

use Mr\Bootstrap\Service\BaseHttpService;
use Mr\Sdk\Model\Account\OAuthToken;
use Mr\Sdk\Model\Account\User;
use Mr\Sdk\Model\Account\Vendor;
use Mr\Sdk\Repository\Account\OAuthTokenRepository;
use Mr\Sdk\Repository\Account\UserRepository;
use Mr\Sdk\Repository\Account\VendorRepository;
use Mr\Sdk\Repository\Account\CredentialRepository;

class AccountService extends BaseHttpService
{
    /**
     * Returns user by id
     *
     * @param $id
     * @return User
     */
    public function getUser($id)
    {
        return $this->getRepository(UserRepository::class)->get($id);
    }

    /**
     * Returns all users matching filters
     *
     * @param array $filters
     * @return array
     */
    public function findUsers(array $filters = [])
    {
        return $this->getRepository(UserRepository::class)->all($filters);
    }

    /**
     * Create new user instance. Does not persist user.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data = [])
    {
        return $this->getRepository(UserRepository::class)->create($data);
    }

    /**
     * Returns first user matching filters
     *
     * @param array $filters
     * @return User
     */
    public function findOneUser(array $filters = [])
    {
        return $this->getRepository(UserRepository::class)->one($filters);
    }

    /**
     * Returns user by id
     *
     * @param $id
     * @return Vendor
     */
    public function getVendor($id)
    {
        return $this->getRepository(VendorRepository::class)->get($id);
    }

    /**
     * Returns all vendors matching filters
     *
     * @param array $filters
     * @return array
     */
    public function findVendors(array $filters = [])
    {
        return $this->getRepository(VendorRepository::class)->all($filters);
    }

    /**
     * Returns first vendor matching filters
     *
     * @param array $filters
     * @return Vendor
     */
    public function findOneVendor(array $filters = [])
    {
        return $this->getRepository(VendorRepository::class)->one($filters);
    }

    /**
     * @param array $data
     * @return Vendor
     */
    public function createVendor(array $data = [])
    {
        return $this->getRepository(VendorRepository::class)->create($data);
    }

    public function getOAuthToken($id)
    {
        return $this->getRepository(UserRepository::class)->get($id);
    }

    /**
     * @param $provider
     * @return OAuthToken
     */
    public function getOAuthTokenByProvider($provider, $liveMode = true)
    {
        return $this->getRepository(OAuthTokenRepository::class)->getByProvider($provider, $liveMode);
    }

    /**
     * Returns credential by id
     *
     * @param $id
     * @return Vendor
     */
    public function getCredential($id)
    {
        return $this->getRepository(CredentialRepository::class)->get($id);
    }
}