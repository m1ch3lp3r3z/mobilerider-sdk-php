<?php


namespace Mr\Sdk\Repository\Storage;

use Mr\Bootstrap\Http\Filtering\SimpleQueryBuilder;
use Mr\Bootstrap\Interfaces\HttpDataClientInterface;
use Mr\Bootstrap\Repository\BaseRepository;
use Mr\Sdk\Model\Storage\FtpFile;

class FtpFileRepository extends BaseRepository
{
    public function __construct(HttpDataClientInterface $client, array $options = [])
    {
        $options["queryBuilderClass"] = SimpleQueryBuilder::class;
        parent::__construct($client, $options);
    }

    public function getModelClass()
    {
        return FtpFile::class;
    }

    protected function getResourcePath()
    {
        return "ftp/" . parent::getResourcePath();
    }
}