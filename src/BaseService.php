<?php
/**
 * Created by PhpStorm.
 * User: michel
 * Date: 8/30/17
 * Time: 2:19 PM
 */

namespace Mr\Sdk;


abstract class BaseService
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * Service constructor.
     * @param $token
     * @param array $options
     */
    public function __construct($token, $options = [])
    {
        $definitions = [] + $this->getDefinitions();

        $instances = [
            'Client' => new Client(
                [
                    'base_uri' => $this->getBaseUrl(),
                    'headers' => [
                        'Authorization' => "Bearer $token",
                        'Content-Type' => 'application/json'
                    ]
                ] + ($options['http'] ?? [])
            )
        ] + $this->getInstances();

        $this->factory = new Factory($definitions, $instances);
    }

    abstract protected function getBaseUrl();

    protected function getDefinitions()
    {
        return [];
    }

    protected function getInstances()
    {
        return [];
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
     * @return \Mr\Sdk\Model
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
     * @return \Mr\Sdk\Model
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
     * @return array
     */
    public function find($entity, $filters = [])
    {
        $repo = $this->getRepository($entity);

        return $repo->find($filters);
    }

    public function findOne($entity, $filters = [])
    {
        $repo = $this->getRepository($entity);

        return $repo->findOne($filters);
    }
}