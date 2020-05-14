<?php

namespace Mr\Sdk\Service;

use GuzzleHttp\Exception\RequestException;
use Mr\Bootstrap\Data\JsonEncoder;
use Mr\Bootstrap\Service\BaseHttpService;
use Mr\Sdk\Model\Storage\FtpFile;
use Mr\Sdk\Repository\Storage\FtpFileRepository;

class ViewerService extends BaseHttpService
{
    public function verifySession()
    {
        try {
            $response = $this->client->get('verify');
        } catch (RequestException $ex) {mr_dd($ex->getMessage());
            return false;
        }

        if ($response->getStatusCode() != 200) {
            return false;
        }

        $contents = $response->getBody()->getContents();

        $data = (new JsonEncoder())->decode($contents);

        return isset($data["success"]) && $data["success"] == 1;
    }
}