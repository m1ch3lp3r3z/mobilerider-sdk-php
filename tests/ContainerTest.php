<?php

use Mr\Sdk\Container;
use PHPUnit\Framework\TestCase;


class ContainerTest extends TestCase
{
    private $yesterdayTimestamp;

    /**
     * @var Container
     */
    protected $instance;

    public function setUp()
    {
        $this->yesterdayTimestamp = time() - 24*60*60;

        $definitions = [
            'yesterday' => [
                'single' => true,
                'class' => \DateTime::class,
                'arguments' => [
                    'str' => '@' . $this->yesterdayTimestamp
                ]
            ]
        ];

        $this->instance = new Container($definitions);
    }

    public function testDefinitionAndDependencyResolving()
    {
        $datetime = $this->instance->get('yesterday');

        $this->assertInstanceOf(\DateTime::class, $datetime);

        $this->assertEquals($this->yesterdayTimestamp, $datetime->getTimestamp());
    }
}