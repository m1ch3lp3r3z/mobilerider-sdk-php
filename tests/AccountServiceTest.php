<?php

use PHPUnit\Framework\TestCase;

use Mr\Sdk\Sdk;
use Mr\Sdk\Factory;
use Mr\Sdk\Client;
use Mr\Sdk\Repository;


class AccountServiceTest extends TestCase
{
    /**
     * @var Sdk
     */
    protected $instance;

    public function setUp()
    {
        Sdk::setToken('123');

        $this->instance = Sdk::getService('account');
    }

    public function testConstruct()
    {
        $client = $this->instance->getClient();

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testGetRepository()
    {
        $this->assertInstanceOf(Repository::class, $this->instance->getRepository('User'));
    }
}