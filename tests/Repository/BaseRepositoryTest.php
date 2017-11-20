<?php


use Mr\Sdk\Model\BaseModel;
use Mr\Sdk\Sdk;
use PHPUnit\Framework\TestCase;

use Mr\SdkTest\Mocks\MockClient;


class BaseRepositoryTest extends TestCase
{
    /**
     * @var Factory
     */
    protected $factory;

    public function setUp()
    {
        $definitions = [
            'AccountClient' => new MockClient(['responses' => []])
        ];

        Sdk::setAuthToken('123', [
            'definitions' => $definitions
        ]);
    }

    public function testConstruct()
    {
        $client = Sdk::get('AccountClient');

        $repository = new \Mr\Sdk\Repository\Account\UserRepository($client);
    }

    public function testCreate()
    {
        $repository = Sdk::get(\Mr\Sdk\Repository\Account\UserRepository::class);

        $model = $repository->create();

        $this->assertInstanceOf(BaseModel::class, $model);
    }

    public function testGet()
    {
        $data = [
            'meta_data' => [],
            'data' => [
                'id' => 123,
                'username' => uniqid(),
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => uniqid() . '@test.com',
                'password' => '123',
                'password_confirmation' => '123'
            ]
        ];

        $definitions = [
            'AccountClient' => new MockClient([
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
            ])
        ];

        Sdk::setAuthToken('123', [
            'definitions' => $definitions
        ]);

        $repository = Sdk::get(\Mr\Sdk\Repository\Account\UserRepository::class);

        $result = $repository->get(123, true);

        $this->assertEquals($data['data'], $result);

        $result = $repository->get(123);

        $this->assertInstanceOf(BaseModel::class, $result);

        $this->assertEquals($data['data'], $result->toArray());
    }

//    public function testUserPost()
//    {
//        $data = [
//            'meta' => [],
//            'data' => [
//                'id' => 123,
//                'username' => 'test',
//                'first_name' => 'John',
//                'last_name' => 'Smith',
//                'email' => 'john@test.com',
//            ]
//        ];
//
//        $this->factory->replace(new MockClient([
//            'responses' => [
//                [
//                    'code' => '200',
//                    'headers' => [],
//                    'body' => json_encode($data)
//                ],
//                [
//                    'code' => '200',
//                    'headers' => [],
//                    'body' => json_encode($data)
//                ]
//            ]
//        ]), 'Client');
//
//        /**
//         * @var BaseRepository
//         */
//        $instance = $this->factory->resolve('UserRepository');
//
//        $initial = $data['data'];
//        unset($initial['id']);
//
//        $model = $instance->create($initial);
//        $model->save();
//
//        $this->assertEquals($data['data'], $model->toArray());
//
//        $result = $instance->get(123);
//
//        $this->assertInstanceOf(BaseModel::class, $result);
//    }
}
