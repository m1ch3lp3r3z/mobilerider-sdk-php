<?php

use Mr\Sdk\Http\Client;
use Mr\Sdk\Sdk;
use Mr\Sdk\Service\MediaService;
use PHPUnit\Framework\TestCase;


class MediaServiceTest extends TestCase
{
    /**
     * @var MediaService
     */
    protected $instance;

    public function setUp()
    {
        Sdk::setAuthToken('123');

        $this->instance = Sdk::getMediaService();
    }

    public function testConstruct()
    {
        $client = $this->instance->getClient();

        $this->assertInstanceOf(Client::class, $client);
    }
}