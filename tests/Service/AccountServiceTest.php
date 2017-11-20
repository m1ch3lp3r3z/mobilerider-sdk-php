<?php

use Mr\Sdk\Http\Client;
use Mr\Sdk\Sdk;
use Mr\Sdk\Service\AccountService;
use PHPUnit\Framework\TestCase;


class AccountServiceTest extends TestCase
{
    /**
     * @var AccountService
     */
    protected $instance;

    public function setUp()
    {
        Sdk::setAuthToken('123');

        $this->instance = Sdk::getAccountService();
    }

    public function testConstruct()
    {
        $client = $this->instance->getClient();

        $this->assertInstanceOf(Client::class, $client);
    }
}