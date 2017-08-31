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
    /**
     * @var Client
     */
    protected $client;

    public function __construct(Factory $factory, $entity)
    {
        $this->entity = $entity;
        $this->factory = $factory;
        $this->client = $factory->get('Client');
    }

    public function getModel()
    {
        return "{$this->entity}Model";
    }

    /**
     * @param array $data
     * @return object
     */
    public function create(array $data = [])
    {
        return $this->factory->create($this->getModel(), [
            'data' => $data
        ]);
    }

    protected function sanitize($entity)
    {
        return strtolower(str_replace(' ', '-', $entity));
    }

    protected function parse(array $data)
    {
        $meta = $data['meta'];

        if (isset($data['data'])) {
            $data = $data['data'];
        } else if (isset($data['object'])) {
            $data = $data['object'];
        } else {
            $data = $data['objects'];
        }

        return [$data, $meta];
    }

    public function get($id, $asArray = false)
    {
        $resource = $this->sanitize($this->entity);

        $data = $this->client->getArray("$resource/$id");

        list($data, $meta) = $this->parse($data);

        return $asArray ? $data : $this->create($data);
    }

    public function findOne(array $filters, $asArray = false)
    {
        $result = $this->find($filters, ['limit' => 1], $asArray);

        return count($result) > 0 ? $result[0] : null;
    }

    public function find(array $filters, array $params = [], $asArray = false)
    {
        $resource = $this->sanitize($this->entity);

        $data = $this->client->getArray($resource, [
            'query' => $filters + $params
        ]);

        list($data, $meta) = $this->parse($data);

        if ($asArray) {
            return $data;
        }

        return array_map(function($item) {
            return $this->create($item);
        }, $data);
    }

    public function getEntity()
    {
        return $this->entity;
    }
}
