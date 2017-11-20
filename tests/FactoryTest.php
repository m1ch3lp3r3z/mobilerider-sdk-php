<?php

use PHPUnit\Framework\TestCase;

use \Mr\Sdk\Factory;

class FactoryTest extends TestCase
{
    public function testDefinitionAndDependencyResolving()
    {
        $obj = Factory::create(\ArrayObject::class, ['input' => ['test' => 'ok']]);

        $this->assertInstanceOf(ArrayObject::class, $obj);
        $this->assertArrayHasKey('test', $obj);
    }
}