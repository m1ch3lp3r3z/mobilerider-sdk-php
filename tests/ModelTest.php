<?php


use Mr\SdkTest\Mocks\MockClient;
use PHPUnit\Framework\TestCase;

use Mr\Sdk\Factory;
use Mr\Sdk\Model;
use Mr\Sdk\Repository;


class ModelTest extends TestCase
{
    /**
     * @var Factory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new Factory(
            [
                'MediaRepository' => [
                    Repository::class, [
                        'factory' => 'Factory',
                        'entity' => ['value' => 'Media']
                    ]
                ],
                'MediaModel' => [
                    Model::class, [
                        'repository' => 'MediaRepository',
                        'data' => null
                    ]
                ]
            ]
        );

        $this->factory->register(new MockClient(['responses' => []]), 'Client');
    }

    public function testConstruct()
    {
        $data = [
            'id' => 123,
            'title' => 'Testing Media 123'
        ];

        /**
         * @var Model
         */
        $instance = $this->factory->create('MediaModel', ['data' => $data]);

        $this->assertInstanceOf(Model::class, $instance);
        $this->assertEquals($data, $instance->toArray());
    }
}