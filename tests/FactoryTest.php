<?php

use PHPUnit\Framework\TestCase;

use Mr\Sdk\Client;
use Mr\Sdk\Repository;
use \Mr\Sdk\Factory;

class FactoryTest extends TestCase
{
    /**
     * @var Factory
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = new Factory();
    }

    public function testRegister()
    {
        $instance = new stdClass();

        $this->instance->register($instance, 'stdClass');

        $this->assertEquals($instance, $this->instance->get('stdClass'));
    }

    public function testRegisterClassInstanceNoName()
    {
        $instance = new \DateTime();

        $this->instance->register($instance);

        $this->assertEquals($instance, $this->instance->get('DateTime'));
    }

    public function testDefinitionAndDependencyResolving()
    {
        $this->assertNull($this->instance->get('Client', false));

        $this->instance->register(new Client(), 'Client');

        $this->instance->define('Repository', Repository::class, [
            'factory' => 'Factory',
            'entity' => null
            ]
        );

        $this->assertInstanceOf(Repository::class, $this->instance->resolve('Repository', ['entity' => 'Media']));
    }
}