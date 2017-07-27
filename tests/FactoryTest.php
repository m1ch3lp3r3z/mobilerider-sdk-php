<?php

use Mr\Sdk\Client;
use Mr\Sdk\Repository;
use PHPUnit\Framework\TestCase;

use \Mr\Sdk\Factory;

class FactoryTest extends TestCase
{
    public function testRegister()
    {
        $instance = new stdClass();

        Factory::register($instance, 'stdClass');

        $this->assertEquals($instance, Factory::get('stdClass'));
    }

    public function testRegisterClassInstanceNoName()
    {
        $instance = new \DateTime();

        Factory::register($instance);

        $this->assertEquals($instance, Factory::get('DateTime'));
    }

    public function testDefinitionAndDependencyResolving()
    {
        Factory::register(new Client(), 'Client');

        Factory::define('Repository', Repository::class, [
            'client' => 'Client',
            'entity' => 'string'
            ]
        );

        $this->assertInstanceOf(Repository::class, Factory::resolve('Repository', ['entity' => 'Media']));
    }
}