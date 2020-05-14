<?php


namespace Mr\Sdk\Repository\Viewer;

use Mr\Bootstrap\Http\Filtering\SimpleQueryBuilder;
use Mr\Bootstrap\Interfaces\HttpDataClientInterface;
use Mr\Bootstrap\Repository\BaseRepository;
use Mr\Sdk\Model\Viewer\Viewer;

class ViewerRepository extends BaseRepository
{
    public function __construct(HttpDataClientInterface $client, array $options = [])
    {
        $options["queryBuilderClass"] = SimpleQueryBuilder::class;
        parent::__construct($client, $options);
    }

    public function getModelClass()
    {
        return Viewer::class;
    }

    public function getByOriginalId($id)
    {
        $resource = $this->getResourcePath();
        $uri = "{$resource}/original/$id";

        $data = $this->client->getData($uri);
        $data = $this->parseOne($data);

        if (! $data) {
            return null;
        }

        return $this->create($data);
    }
}