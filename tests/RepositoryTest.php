<?php


use PHPUnit\Framework\TestCase;

use Mr\Sdk\Factory;
use Mr\Sdk\Repository;
use Mr\Sdk\Model;
use Mr\SdkTest\Mocks\MockClient;


class RepositoryTest extends TestCase
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
        /**
         * @var Repository
         */
        $instance = $this->factory->resolve('MediaRepository');

        $this->assertEquals('Media', $instance->getEntity());
    }

    public function testCreate()
    {
        $instance = $this->factory->resolve('MediaRepository');

        $this->assertInstanceOf(Model::class, $instance->create());
    }

    public function testGet()
    {
        $data = [
            'meta' => [
                'request' =>
                    [
                        'method' => "GET"
                    ]
            ],
            'object' => [
                'id' => 123,
                'title' => 'Testing media'
            ]
        ];

        $this->factory->replace(new MockClient([
            'responses' => [
                [
                    'code' => '200',
                    'headers' => [],
                    'body' => json_encode($data)
                ],
                [
                    'code' => '200',
                    'headers' => [],
                    'body' => json_encode($data)
                ]
            ]
        ]), 'Client');

        /**
         * @var Repository
         */
        $instance = $this->factory->resolve('MediaRepository');

        $result = $instance->get(123, true);

        $this->assertEquals($data['object'], $result);

        $result = $instance->get(123);

        $this->assertInstanceOf(Model::class, $result);
    }
}
