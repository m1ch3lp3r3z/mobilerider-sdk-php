<?php

namespace Mr\Sdk;


use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use Mr\Sdk\Exception\InvalidCredentialsException;
use Mr\Sdk\Exception\MrException;
use Mr\Sdk\Http\Client;
use Mr\Sdk\Http\Middleware\AuthMiddleware;
use Mr\Sdk\Http\Middleware\ErrorsMiddleware;
use Mr\Sdk\Interfaces\ContainerAccessorInterface;
use Mr\Sdk\Model\Account\OAuthToken;
use Mr\Sdk\Model\Account\User;
use Mr\Sdk\Model\Account\Vendor;
use Mr\Sdk\Model\Media\Media;
use Mr\Sdk\Repository\Account\OAuthTokenRepository;
use Mr\Sdk\Repository\Account\UserRepository;
use Mr\Sdk\Repository\Account\VendorRepository;
use Mr\Sdk\Repository\Media\MediaRepository;
use Mr\Sdk\Service\MediaService;
use Mr\Sdk\Service\AccountService;
use Mr\Sdk\Traits\ContainerAccessor;

/**
 * @method static object get($name, $args = [])
 * @method static MediaService getMediaService
 * @method static AccountService getAccountService
 *
 * Class Sdk
 * @package Mr\Sdk
 */
class Sdk implements ContainerAccessorInterface
{
    use ContainerAccessor;

    private static $instance;

    private $accountId;
    private $appId;
    private $appSecret;
    private $token;
    private $options;

    private $defaultHeaders = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
    ];

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * Service constructor.
     * @param $accountId
     * @param $appId
     * @param $appSecret
     * @param string $token
     * @param array $options
     * @param array $definitions
     * @throws MrException
     */
    private function __construct($accountId, $appId, $appSecret, $token = null, array $options = [])
    {
        $this->accountId = $accountId;
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->token = $token;
        $this->options = $options;

        if ((!$accountId || !$appId || !$appSecret) && !$token) {
            throw new MrException('Empty credentials');
        }

        if (!$token) {
            $this->authenticate();
        }

        // Create default handler with all the default middlewares
        $stack = HandlerStack::create();
        $stack->remove('http_errors');
        $stack->unshift(new AuthMiddleware($this->token), 'auth');

        // Last to un-shift so it remains first to execute
        $stack->unshift(new ErrorsMiddleware([]), 'http_errors');
        $httpOptions = [
            'handler' => $stack,
            'headers' => $this->defaultHeaders
        ];

        $customDefinitions = isset($options['definitions']) ? $options['definitions'] : [];

        $definitions = $customDefinitions + [
                'Logger' => [
                    'single' => true,
                    'class' => Logger::class,
                ],
                // Clients
                'MediaClient' => [
                    'single' => true,
                    'class' => Client::class,
                    'arguments' => [
                        'options' => ['base_uri' => MediaService::BASE_URL] + $httpOptions
                    ]
                ],
                'AccountClient' => [
                    'single' => true,
                    'class' => Client::class,
                    'arguments' => [
                        'options' => ['base_uri' => AccountService::BASE_URL] + $httpOptions
                    ]
                ],
                // Services
                MediaService::class => [
                    'single' => true,
                    'class' => MediaService::class,
                    'arguments' => [
                        'client' => 'MediaClient'
                    ]
                ],
                AccountService::class => [
                    'single' => true,
                    'class' => AccountService::class,
                    'arguments' => [
                        'client' => 'AccountClient'
                    ]
                ],
                // Repositories
                MediaRepository::class => [
                    'single' => true,
                    'class' => MediaRepository::class,
                    'arguments' => [
                        'client' => 'MediaClient'
                    ]
                ],
                UserRepository::class => [
                    'single' => true,
                    'class' => UserRepository::class,
                    'arguments' => [
                        'client' => 'AccountClient'
                    ]
                ],
                VendorRepository::class => [
                    'single' => true,
                    'class' => VendorRepository::class,
                    'arguments' => [
                        'client' => 'AccountClient'
                    ]
                ],
                OAuthTokenRepository::class => [
                    'single' => true,
                    'class' => OAuthTokenRepository::class,
                    'arguments' => [
                        'client' => 'AccountClient'
                    ]
                ],
                // Models
                Media::class => [
                    'single' => true,
                    'class' => Media::class,
                    'arguments' => [
                        'repository' => 'UserRepository',
                        'data' => null
                    ]
                ],
                User::class => [
                    'single' => false,
                    'class' => User::class,
                    'arguments' => [
                        'repository' => 'UserRepository',
                        'data' => null
                    ]
                ],
                Vendor::class => [
                    'single' => false,
                    'class' => Vendor::class,
                    'arguments' => [
                        'repository' => 'VendorRepository',
                        'data' => null
                    ]
                ],
                OAuthToken::class => [
                    'single' => false,
                    'class' => OAuthToken::class,
                    'arguments' => [
                        'repository' => OAuthTokenRepository::class,
                        'data' => null
                    ]
                ]
            ];

        $this->container = new Container($definitions);
    }

    protected function isDebug()
    {
        return $this->options['debug'] ?? false;
    }

    protected function authenticate()
    {
        $client = new Client([
            'base_uri' => AccountService::BASE_URL,
            'headers' => $this->defaultHeaders
        ]);

        $data = null;

        try {
            $data = $client->postData('user/authenticate', [
                'vendor_uuid' => $this->accountId,
                'username' => $this->appId,
                'password' => $this->appSecret
            ]);
        } catch (RequestException $ex) {
            // Just avoid request exception from propagating
            if ($this->isDebug()) {
                $this->_get('Logger')->log($ex->getMessage());
            }
        }

        if (!isset($data, $data['data'], $data['data']['token'])) {
            throw new InvalidCredentialsException();
        }

        return $this->token = $data['data']['token'];
    }

    public function getAccountId()
    {
        return $this->accountId;
    }

    public function getTokenFor($userId)
    {
        $client = new Client([
            'base_uri' => AccountService::BASE_URL,
            'headers' => $this->defaultHeaders + [
                    AuthMiddleware::AUTH_HEADER => "Bearer {$this->token}"
                ]
        ]);

        $data = null;

        try {
            $data = $client->postData("user/$userId/impersonate", []);
        } catch (RequestException $ex) {
            // Just avoid request exception from propagating
            if ($this->isDebug()) {
                $this->_get('Logger')->log($ex->getMessage());
            }
        }

        if (!isset($data, $data['data'], $data['data']['token'])) {
            throw new InvalidCredentialsException();
        }

        return $data['data']['token'];
    }

    public static function impersonate($userId)
    {
        $token = self::getInstance()->getTokenFor($userId);

        self::create(null, null, null, $token);
    }

    protected static function create($accountId, $appId, $appSecret, $token = null, array $options = [])
    {
        self::$instance = new self($accountId, $appId, $appSecret, $token, $options);
    }

    public static function setCredentials($accountId, $appId, $appSecret, $token = null, array $options = [])
    {
        self::create($accountId, $appId, $appSecret, $token, $options);
    }

    public static function setAuthToken($token, array $options = [])
    {
        self::create(null,null, null, $token, $options);
    }

    /**
     * @return Sdk
     */
    protected static function getInstance()
    {
        if (!self::$instance) {
            throw new \RuntimeException('You need to set credentials or auth token first');
        }

        return self::$instance;
    }

    public static function __callStatic($name, $arguments)
    {
        $instance = self::getInstance();

        $name = '_' . $name;

        return call_user_func_array([$instance, $name], $arguments);
    }

    /**
     * @return MediaService
     * @internal param $name
     */
    protected function _getMediaService()
    {
        return $this->_get(MediaService::class);
    }

    protected function _getAccountService()
    {
        return $this->_get(AccountService::class);
    }
}
