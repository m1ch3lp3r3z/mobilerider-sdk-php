<?php

namespace Mr\Sdk\Service;

use Mr\Sdk\Model\Media\Media;
use Mr\Sdk\Repository\Media\MediaRepository;

class MediaService extends BaseService
{
    const BASE_URL = 'https://api.mobilerider.com/api/';
    const APP_VENDOR_HEADER = 'X-Vendor-App-Id';

    protected function getBaseUrl()
    {
        return self::BASE_URL;
    }

    /**
     * Returns media by id
     *
     * @param $id
     * @return Media
     */
    public function getMedia($id)
    {
        return $this->_get(MediaRepository::class)->get($id);
    }

    /**
     * Returns all medias matching filters
     *
     * @param array $filters
     * @return array
     */
    public function findMedias(array $filters = [])
    {
        return $this->_get(MediaRepository::class)->find($filters);
    }

    /**
     * Returns first media matching filters
     *
     * @param array $filters
     * @return mixed
     */
    public function findOneMedia(array $filters = [])
    {
        return $this->_get(MediaRepository::class)->findOne($filters);
    }

    /**
     * Create new media instance. Does not persist media.
     *
     * @param array $data
     * @return Media
     */
    public function createMedia(array $data = [])
    {
        return $this->_get(MediaRepository::class)->create($data);
    }
}
