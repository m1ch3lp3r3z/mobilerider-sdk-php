<?php
/**
 * Created by PhpStorm.
 * User: michel
 * Date: 10/27/17
 * Time: 9:46 AM
 */

namespace Mr\Sdk;


use Mr\Sdk\Interfaces\LoggerInterface;

class Logger implements LoggerInterface
{
    public function log($msg)
    {
        print $msg . PHP_EOL;
    }
}