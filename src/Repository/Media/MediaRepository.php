<?php


namespace Mr\Sdk\Repository\Media;

use Mr\Bootstrap\Http\Filtering\MrApiQueryBuilder;
use Mr\Bootstrap\Interfaces\HttpDataClientInterface;
use Mr\Bootstrap\Repository\BaseRepository;
use Mr\Sdk\Model\Media\Media;

class MediaRepository extends BaseRepository
{
    public function __construct(HttpDataClientInterface $client, array $options = [])
    {
        $options["queryBuilderClass"] = MrApiQueryBuilder::class;
        parent::__construct($client, $options);   
    }
    public function getModelClass()
    {
        return Media::class;
    }

    protected function getResourcePath()
    {
        return $this->getResource();
    }

    public function parseOne(array $data, array &$metadata = [])
    {
        $metadata = $data['meta'];

        return $data['object'] ?? $data["objects"]; // POST will return `objects`
    }

    public function parseMany(array $data, array &$metadata = [])
    {
        $metadata = $data['meta'];

        return $data['objects'];
    }

    protected function buildQuery(array $filters, array $params)
    {
        return $filters + $params;
    }
}