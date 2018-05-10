<?php

namespace Mr\Sdk\Service;


use Mr\Bootstrap\Service\BaseHttpService;
use Mr\Sdk\Repository\Media\MediaRepository;

class MediaService extends BaseHttpService
{
    const APP_VENDOR_HEADER = 'X-Vendor-App-Id';

    /**
     * Returns media by id
     *
     * @param $id
     * @return Media
     */
    public function getMedia($id)
    {
        return $this->getRepository(MediaRepository::class)->get($id);
    }

    /**
     * Returns all medias matching filters
     *
     * @param array $filters
     * @return array
     */
    public function findMedias(array $filters = [])
    {
        return $this->getRepository(MediaRepository::class)->all($filters);
    }

    /**
     * Returns first media matching filters
     *
     * @param array $filters
     * @return mixed
     */
    public function findOneMedia(array $filters = [])
    {
        return $this->getRepository(MediaRepository::class)->findOne($filters);
    }

    /**
     * Create new media instance. Does not persist media.
     *
     * @param array $data
     * @return Media
     */
    public function createMedia(array $data = [])
    {
        return $this->getRepository(MediaRepository::class)->create($data);
    }
}
