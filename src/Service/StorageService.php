<?php

namespace Mr\Sdk\Service;

use Mr\Bootstrap\Service\BaseHttpService;
use Mr\Sdk\Model\Storage\FtpFile;
use Mr\Sdk\Repository\Storage\FtpFileRepository;

class StorageService extends BaseHttpService
{
    /**
     * Returns list of ftp files
     *
     * @return FtpFile[]
     */
    public function findFtpFiles(array $filters = [])
    {
        return $this->getRepository(FtpFileRepository::class)->all($filters);
    }

    protected function buildQuery(array $filters, array $params)
    {
        return $filters + $params;
    }
}