<?php
/**
 * Created by PhpStorm.
 * User: michel
 * Date: 8/31/17
 * Time: 2:30 PM
 */

use Mr\Sdk\Sdk;

class SdkTest
{
    public function testAttemptToGetServiceWithNoTokenShouldFail()
    {

    }

    public function testTokenChangeShouldResetInstances()
    {
        Sdk::setToken('123');

        $service = Sdk::getService('media');

        $this->assertEquals($service, Sdk::getService('media'));

        Sdk::setToken('123');

        $this->assertNotEquals($service, Sdk::getService('media'));
    }
}