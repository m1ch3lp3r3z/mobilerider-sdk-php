<?php


namespace Mr\Sdk\Repository\Media;


use Mr\Bootstrap\Repository\BaseRepository;
use Mr\Sdk\Model\Media\Media;

class MediaRepository extends BaseRepository
{
    public function getModelClass()
    {
        return Media::class;
    }

    public function parseOne(array $data, array &$metadata = [])
    {
        $metadata = $data['meta'];

        return $data['object'];
    }

    public function parseAll(array $data, array &$metadata = [])
    {
        $metadata = $data['meta'];

        return $data['objects'];
    }

    protected function buildQuery(array $filters, array $params)
    {
        return $filters + $params;
    }
}