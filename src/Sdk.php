<?php

namespace Mr\Sdk;


use Mr\Sdk\Service\MediaService;
use Mr\Sdk\Service\AccountService;

/**
 * Class Sdk
 * @package Mr\Sdk
 */
class Sdk
{
    private static $token;
    private static $instance;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * Service constructor.
     * @param string $token
     */
    private function __construct($token)
    {
        $definitions = [
            'MediaService' => [
                MediaService::class, [
                    'token' => ['value' => $token]
                ]
            ],
            'AccountService' => [
                AccountService::class, [
                    'token' => ['value' => $token]
                ]
            ]
        ];

        $instances = [];

        $this->factory = new Factory($definitions, $instances);
    }

    public static function setToken($token)
    {
        self::$token = $token;

        self::$instance = null;
    }

    protected static function getInstance()
    {
        if (!self::$instance) {
            if (!self::$token) {
                throw new \RuntimeException('Token must be set');
            }

            self::$instance = new self(self::$token);
        }

        return self::$instance;
    }

    public static function __callStatic($name, $arguments)
    {
        $instance = self::getInstance();

        return call_user_func_array([$instance, $name], $arguments);
    }

    /**
     * @param $name
     * @return BaseService
     * @throws \Exception
     */
    protected function getService($name)
    {
        switch($name) {
            case 'media': return $this->factory->resolve('MediaService');
            case 'account': return $this->factory->resolve('AccountService');
        }

        throw new \Exception('Service not found');
    }
}
