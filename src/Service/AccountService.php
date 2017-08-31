<?php
/**
 * Created by PhpStorm.
 * User: michel
 * Date: 8/30/17
 * Time: 12:21 PM
 */

namespace Mr\Sdk\Service;

use Mr\Sdk\BaseService;
use Mr\Sdk\Model;
use Mr\Sdk\Repository;

class AccountService extends BaseService
{
    const API_BASE_URL = 'http://localhost:3001/api/v1/';

    protected function getBaseUrl()
    {
        return self::API_BASE_URL;
    }

    public function getDefinitions()
    {
        return [
            'UserRepository' => [
                Repository::class, [
                'factory' => 'Factory',
                'entity' => ['value' => 'User']
            ]],
            'UserModel' => [
                Model::class, [
                    'repository' => 'UserRepository',
                    'data' => null
                ]
            ]
        ];
    }

    public function getUser($id)
    {
        return $this->get('User', $id);
    }

    public function getChannel($id)
    {
        return $this->get('Channel', $id);
    }

    /**
     * Returns all media objects
     *
     * @param array $filters
     * @return array
     */
    public function findUsers($filters = [])
    {
        return $this->find('User', $filters);
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function findOneUser($filters = [])
    {
        return $this->findOne('User', $filters);
    }

    /**
     * Returns all channel objects
     *
     * @param array $filters
     * @return array
     */
    public function findChannels($filters = [])
    {
        return $this->getAll('Channel', $filters);
    }


}