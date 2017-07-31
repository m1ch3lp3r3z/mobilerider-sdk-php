<?php

use PHPUnit\Framework\TestCase;

use Mr\Sdk\Service;
use Mr\Sdk\Factory;
use Mr\Sdk\Client;
use Mr\Sdk\Repository;


class ServiceTest extends TestCase
{
    /**
     * @var Service
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = new Service('', '', []);
    }

    public function testConstruct()
    {
        $instance = new Service('test', '123', []);
        $client = $instance->getClient();

        $this->assertInstanceOf(Client::class, $client);

        $instance = new Service('another test', '12345', []);
        $this->assertNotEquals($client, $instance->getClient());
    }

    public function testGetRepository()
    {
        $this->assertInstanceOf(Repository::class, $this->instance->getRepository('Media'));
    }
}