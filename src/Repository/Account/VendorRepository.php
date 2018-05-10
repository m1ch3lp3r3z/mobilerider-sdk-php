<?php


namespace Mr\Sdk\Repository\Account;


use Mr\Bootstrap\Repository\BaseRepository;
use Mr\Sdk\Model\Account\Vendor;

class VendorRepository extends BaseRepository
{
    public function getModelClass()
    {
        return Vendor::class;
    }
}