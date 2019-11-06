<?php

namespace Mr\Sdk\Model\Storage;

use Mr\Bootstrap\Model\BaseModel;

class FtpFile extends BaseModel
{
    public static function getResource()
    {
        return 'file';
    }
}