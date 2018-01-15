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
    private $httpOptions;

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
     * @param array $httpOptions
     * @throws MrException
     */
    private function __construct($accountId, $appId, $appSecret, $token = null, array $options = [], array $httpOptions = [])
    {
        $this->accountId = $accountId;
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->token = $token;
        $this->options = $options;
        $this->httpOptions = [
            'account' => array_merge(
                [
                    'base_uri' => AccountService::BASE_URL,
                    'headers' => $this->defaultHeaders
                ],
                $httpOptions['account'] ?? []
            ),
            'media' => array_merge(
                [
                    'base_uri' => MediaService::BASE_URL,
                    'headers' => $this->defaultHeaders
                ],
                $httpOptions['media'] ?? []
            ),
        ];

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
        $httpDefaultRuntimeOptions = [
            'handler' => $stack,
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
                        'options' => array_merge($httpDefaultRuntimeOptions, $this->httpOptions['media'])
                    ]
                ],
                'AccountClient' => [
                    'single' => true,
                    'class' => Client::class,
                    'arguments' => [
                        'options' => array_merge($httpDefaultRuntimeOptions, $this->httpOptions['account'])
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
        $client = new Client($this->httpOptions['account']);

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

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function getHttpOptions()
    {
        return $this->httpOptions;
    }

    public function getTokenFor($userId)
    {
        $httpOptions = $this->httpOptions['account'];
        $httpOptions['headers'] = array_merge($httpOptions['headers'], [
                AuthMiddleware::AUTH_HEADER => "Bearer {$this->token}"
            ]);

        $client = new Client($httpOptions);
        $data = null;

        try {
            $data = $client->postData("user/$userId/impersonate", []);
        } catch (RequestException $ex) {
            // Just avoid request exception from propagating
            if ($this->isDebug()) {
                $this->_get('Logger')->log($ex->getMessage());
            }
        }

        if (!$data || !isset($data['data']['token'])) {
            throw new InvalidCredentialsException();
        }

        return $data['data']['token'];
    }

    public static function impersonate($userId, $options = null, $httpOptions = null)
    {
        $instance = self::getInstance();

        $options = is_null($options) ? $instance->getOptions() : $options;
        $httpOptions = is_null($httpOptions) ? $instance->getHttpOptions() : $httpOptions;
        $token = $instance->getTokenFor($userId);

        self::create(null, null, null, $token, $options, $httpOptions);
    }

    protected static function create($accountId, $appId, $appSecret, $token, array $options, array $httpOptions)
    {
        self::$instance = new self($accountId, $appId, $appSecret, $token, $options, $httpOptions);
    }

    public static function setCredentials($accountId, $appId, $appSecret, $token = null, array $options = [], array $httpOptions = [])
    {
        self::create($accountId, $appId, $appSecret, $token, $options, $httpOptions);
    }

    public static function setAuthToken($token, array $options = [], array $httpOptions = [])
    {
        self::create(null,null, null, $token, $options, $httpOptions);
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

    /**
     * @return AccountService
     */
    protected function _getAccountService()
    {
        return $this->_get(AccountService::class);
    }
}
