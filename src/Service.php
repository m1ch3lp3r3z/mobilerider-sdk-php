<?php

namespace Mr\Sdk;


class Service
{
    const API_BASE_URL = 'https://api.mobilerider.com/api/';
    const APP_VENDOR_HEADER = 'X-Vendor-App-Id';

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * Service constructor.
     * @param $appId
     * @param $appSecret
     * @param array $options
     */
    public function __construct($appId, $appSecret, $options = array())
    {
        $definitions = [
            'MediaRepository' => [Repository::class, [
                'factory' => 'Factory',
                'entity' => ['value' => 'Media']
            ]],
            'MediaModel' => [
                Model::class, [
                    'repository' => 'MediaRepository',
                    'data' => null
                ]
            ]
        ];

        $instances = [
            'Client' => new Client(
                [
                    'base_uri' => self::API_BASE_URL,
                    'auth' => [$appId, $appSecret],
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ]
                ] + ($options['http'] ?? [])
            )
        ];

        $this->factory = new Factory($definitions, $instances);
    }

    public function getClient()
    {
        return $this->factory->get('Client');
    }

    public function getRepository($entity)
    {
        return $this->factory->resolve("{$entity}Repository");
    }

    /**
     * Returns a new object from given model and initial data.
     * It does not execute any persistent action.
     *
     * @param $entity string
     * @param $data object | array
     * @return Mr\Api\Model\ApiObject
     */
    public function create($entity, $data = null)
    {
        $repo = $this->getRepository($entity);

        return $repo->create($data);
    }

    /**
     * Returns an object by its given model and id.
     *
     * @param $entity string
     * @param $id mixed
     * @return Mr\Api\Model\ApiObject
     */
    public function get($entity, $id)
    {
        $repo = $this->getRepository($entity);

        return $repo->get($id);
    }

    /**
     * Returns a all objects from given model.
     *
     * @param string $entity
     * @param array $filters
     * @return ApiObjectCollection
     */
    public function find($entity, $filters = array())
    {
        $repo = $this->getRepository($entity);

        return $repo->getAll($filters);
    }

    // Helpers

    public function getMedia($id)
    {
        return $this->get('Media', $id);
    }

    public function getChannel($id)
    {
        return $this->get('Channel', $id);
    }

    /**
     * Returns all media objects
     *
     * @param array $filters
     * @return ApiObjectCollection
     */
    public function findMedias($filters = array())
    {
        return $this->getAll('Media', $filters);
    }

    /**
     * Returns all channel objects
     *
     * @param array $filters
     * @return ApiObjectCollection
     */
    public function findChannels($filters = array())
    {
        return $this->getAll('Channel', $filters);
    }
}
