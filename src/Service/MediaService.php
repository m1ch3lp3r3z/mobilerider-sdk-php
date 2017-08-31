<?php

namespace Mr\Sdk\Service;

use Mr\Sdk\BaseService;
use Mr\Sdk\Repository;

class MediaService extends BaseService
{
    const API_BASE_URL = 'https://api.mobilerider.com/api/';
    const APP_VENDOR_HEADER = 'X-Vendor-App-Id';

    protected function getBaseUrl()
    {
        return self::API_BASE_URL;
    }

    public function getDefinitions()
    {
        return [
            'MediaRepository' => [
                Repository::class, [
                'factory' => 'Factory',
                'entity' => ['value' => 'Media']
            ]],
            'MediaModel' => [
                Model::class, [
                    'repository' => 'MediaRepository',
                    'data' => null
                ]
            ]
        ];
    }

    public function getMedia($id)
    {
        return $this->get('Media', $id);
    }

    public function getChannel($id)
    {
        return $this->get('Channel', $id);
    }

    /**
     * Returns all media objects
     *
     * @param array $filters
     * @return array
     */
    public function findMedias($filters = [])
    {
        return $this->find('Media', $filters);
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function findOneMedia($filters = [])
    {
        return $this->findOne('Media', $filters);
    }

    /**
     * Returns all channel objects
     *
     * @param array $filters
     * @return array
     */
    public function findChannels($filters = [])
    {
        return $this->getAll('Channel', $filters);
    }


}
