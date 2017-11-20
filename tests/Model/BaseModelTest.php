<?php


use Mr\Sdk\Sdk;
use Mr\SdkTest\Mocks\MockClient;
use PHPUnit\Framework\TestCase;


class BaseModelTest extends TestCase
{
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
        $data = [
            'username' => uniqid(),
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => uniqid() . '@test.com',
            'password' => '123',
            'password_confirmation' => '123'
        ];

        $repository = Sdk::get(\Mr\Sdk\Repository\Account\UserRepository::class);

        $model = new \Mr\Sdk\Model\Account\User($repository, $data);

        $this->assertEquals($data, $model->toArray());
    }
}