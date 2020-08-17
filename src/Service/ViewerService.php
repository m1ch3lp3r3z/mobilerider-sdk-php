<?php

namespace Mr\Sdk\Service;

use GuzzleHttp\Exception\RequestException;
use Mr\Bootstrap\Data\JsonEncoder;
use Mr\Bootstrap\Service\BaseHttpService;
use Mr\Sdk\Repository\Viewer\ViewerRepository;
use Mr\Sdk\Model\Viewer\Viewer;

class ViewerService extends BaseHttpService
{
    public function verifySession()
    {
        try {
            $response = $this->client->get('verify');
        } catch (RequestException $ex) {
            return false;
        }

        if ($response->getStatusCode() != 200) {
            return false;
        }

        $contents = $response->getBody()->getContents();

        $data = (new JsonEncoder())->decode($contents);

        return isset($data["success"]) && $data["success"] == 1;
    }

    public function endSession()
    {
        try {
            $response = $this->client->delete('sessions');
        } catch (RequestException $ex) {
            return false;
        }

        if ($response->getStatusCode() != 200) {
            return false;
        }

        return true;
    }

    public function getViewer($id)
    {
        return $this->getRepository(ViewerRepository::class)->get($id);
    }

    /**
     * Returns viewer by external identifier. 
     * If it doesn't exist it is created
     *
     * @param $id
     * @return Viewer
     */
    public function getViewerByOriginalId($id)
    {
        return $this->getRepository(ViewerRepository::class)->getByOriginalId($id);
    }
}