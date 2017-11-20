<?php

namespace Mr\Sdk\Repository;


use Mr\Sdk\Http\Client;
use Mr\Sdk\Interfaces\ContainerAccessorInterface;
use Mr\Sdk\Model\BaseModel;
use Mr\Sdk\Traits\ContainerAccessor;

abstract class BaseRepository implements ContainerAccessorInterface
{
    use ContainerAccessor;

    /**
     * @var Client
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public static function getModelClass()
    {
        throw new \RuntimeException('To be defined by child class');
    }

    /**
     * @param array $data
     * @return object
     */
    public function create(array $data = [])
    {
        return $this->_get(static::getModelClass(), [
            'repository' => $this, // Important to pass current repository and avoid container creating new one
            'data' => $data
        ]);
    }

    public function getUri($id = null)
    {
        $model = static::getModelClass();

        $arr = [$model::getResource()];

        if (!is_null($id)) {
            $arr[] = $id;
        }

        return implode('/', $arr);
    }

    protected function parseOne(array $data, array &$metadata = [])
    {
        $metadata = $data['meta_data'];

        return $data['data'];
    }

    protected function parseMany(array $data, array &$metadata = [])
    {
        $metadata = $data['meta_data'];

        return $data['data'];
    }

    protected function buildQuery(array $filters, array $params)
    {
        $arr = [];

        foreach ($filters as $key => $value) {
            $arr[] = "$key:$value";
        }

        return [
            'search' => implode(';', $arr)
        ] + $params;
    }

    public function get($id, $asArray = false)
    {
        $data = $this->client->getData($this->getUri($id));

        $data = $this->parseOne($data);

        return $asArray ? $data : $this->create($data);
    }

    public function findOne(array $filters, array $params = [], $asArray = false)
    {
        $result = $this->find(
            $filters,
            ['limit' => 1] + $params,
            $asArray
        );

        return count($result) > 0 ? $result[0] : null;
    }

    public function find(array $filters, array $params = [], $asArray = false)
    {
        $data = $this->client->getData($this->getUri(), $this->buildQuery($filters, $params));

        $data = $this->parseMany($data);

        if ($asArray) {
            return $data;
        }

        return array_map(function($item) {
            return $this->create($item);
        }, $data);
    }

    protected function encode(array $data)
    {
        return json_encode($data);
    }

    protected function decode($stream)
    {
        return json_decode($stream, true);
    }

    public function persist(BaseModel $model)
    {
        if ($model->isNew()) {
            $data = $this->client->postData(
                $model->getUri(),
                $model->toArray()
            );
        } else {
            $data = $this->client->putData(
                $model->getUri($model->id),
                $model->toArray()
            );
        }

        $model->fill($data);
    }
}
