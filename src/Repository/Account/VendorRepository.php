<?php


namespace Mr\Sdk\Repository\Account;


use Mr\Sdk\Model\Account\Vendor;
use Mr\Sdk\Repository\BaseRepository;

class VendorRepository extends BaseRepository
{
    public static function getModelClass()
    {
        return Vendor::class;
    }
}