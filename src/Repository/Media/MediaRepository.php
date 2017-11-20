<?php


namespace Mr\Sdk\Repository\Media;


use Mr\Sdk\Model\Media\Media;
use Mr\Sdk\Repository\BaseRepository;

class MediaRepository extends BaseRepository
{
    public static function getModelClass()
    {
        return Media::class;
    }

    protected function parseOne(array $data, array &$metadata = [])
    {
        $metadata = $data['meta'];

        return $data['object'];
    }

    protected function parseMany(array $data, array &$metadata = [])
    {
        $metadata = $data['meta'];

        return $data['objects'];
    }

    protected function buildQuery(array $filters, array $params)
    {
        return $filters + $params;
    }
}