<?php

use Mr\Sdk\Sdk;
use PHPUnit\Framework\TestCase;

class SdkTest extends TestCase
{
    public function testAttemptToGetServiceWithNoTokenShouldFail()
    {

    }

    public function testTokenChangeShouldResetInstances()
    {
        Sdk::setAuthToken('123');

        $service = Sdk::getMediaService();

        $this->assertSame($service, Sdk::getMediaService());

        Sdk::setAuthToken('123');

        $this->assertNotSame($service, Sdk::getMediaService());
    }
}