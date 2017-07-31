<?php
/**
 * Created by PhpStorm.
 * User: michel
 * Date: 7/27/17
 * Time: 9:27 AM
 */

namespace Mr\Sdk;


class Repository
{
    protected $entity;
    protected $factory;
    protected $client;

    public function __construct(Factory $factory, $entity)
    {
        $this->entity = $entity;
        $this->factory = $factory;
        $this->client = $factory->get('Client');
    }

    /**
     * @param array $data
     * @return object
     */
    public function create(array $data = [])
    {
        return $this->factory->create('MediaModel', [
            'data' => $data
        ]);
    }

    protected function sanitize($entity)
    {
        return strtolower(str_replace(' ', '-', $entity));
    }

    public function get($id, $asArray = false)
    {
        $resource = $this->sanitize($this->entity);

        $data = $this->client->getArray("$resource/$id");

        return $asArray ? $data : $this->create($data);
    }

    public function getEntity()
    {
        return $this->entity;
    }
}