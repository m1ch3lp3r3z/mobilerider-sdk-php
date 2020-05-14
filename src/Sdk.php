<?php

namespace Mr\Sdk;


use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use Mr\Bootstrap\Container;
use Mr\Bootstrap\Http\Middleware\TokenAuthMiddleware;
use Mr\Bootstrap\Interfaces\ContainerAccessorInterface;
use Mr\Bootstrap\Traits\ContainerAccessor;
use Mr\Bootstrap\Utils\Logger;
use Mr\Sdk\Exception\InvalidCredentialsException;
use Mr\Sdk\Exception\MrException;
use Mr\Sdk\Http\Client;
use Mr\Sdk\Http\Middleware\ErrorsMiddleware;
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
use Mr\Sdk\Service\StorageService;
use Mr\Sdk\Repository\Account\CredentialRepository;
use Mr\Sdk\Model\Account\Credential;
use Mr\Sdk\Model\Storage\FtpFile;
use Mr\Sdk\Repository\Storage\FtpFileRepository;
use Mr\Sdk\Service\ViewerService;
use Mr\Sdk\Model\Viewer\Viewer;
use Mr\Sdk\Repository\Viewer\ViewerRepository;

/**
 * @method static string getToken
 * @method static MediaService getMediaService
 * @method static AccountService getAccountService
 * @method static StorageService getStorageService
 * @method static ViewerService getViewerService
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

        $httpCommon = [
            "debug" => $this->options["debug"] ?? false
        ];

        $this->httpOptions = [
            'account' => array_merge(
                [
                    'base_uri' => 'https://accounts.mobilerider.com/api/v1/',
                    'headers' => $this->defaultHeaders
                ],
                $httpCommon,
                $httpOptions['account'] ?? []
            ),
            'account-sls' => array_merge(
                [
                    'base_uri' => 'https://accounts-sls.mobilerider.com/api/v1/',
                    'headers' => $this->defaultHeaders
                ],
                $httpCommon,
                $httpOptions['account-sls'] ?? []
            ),
            'media' => array_merge(
                [
                    'base_uri' => 'https://api.mobilerider.com/api/',
                    'headers' => $this->defaultHeaders
                ],
                $httpCommon,
                $httpOptions['media'] ?? []
            ),
            'storage' => array_merge(
                [
                    'base_uri' => 'https://storage-sls.mobilerider.com/api/v1/',
                    'headers' => $this->defaultHeaders
                ],
                $httpCommon,
                $httpOptions['storage'] ?? []
            ),
            'viewer' => array_merge(
                [
                    'base_uri' => 'https://viewer-sls.mobilerider.com/api/v1/',
                    'headers' => $this->defaultHeaders
                ],
                $httpCommon,
                $httpOptions['viewer'] ?? []
            )
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
        $stack->unshift(new TokenAuthMiddleware($this->token), 'auth');

        // Last to un-shift so it remains first to execute
        $stack->unshift(new ErrorsMiddleware([]), 'http_errors');
        $httpDefaultRuntimeOptions = [
            'handler' => $stack,
        ];

        $customDefinitions = isset($options['definitions']) ? $options['definitions'] : [];

        $definitions = $customDefinitions + [
                'Logger' => [
                    'single' => true,
                    'instance' => Logger::getInstance(),
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
                'AccountSlsClient' => [
                    'single' => true,
                    'class' => Client::class,
                    'arguments' => [
                        'options' => array_merge($httpDefaultRuntimeOptions, $this->httpOptions['account-sls'])
                    ]
                ],
                'StorageClient' => [
                    'single' => true,
                    'class' => Client::class,
                    'arguments' => [
                        'options' => array_merge($httpDefaultRuntimeOptions, $this->httpOptions['storage'])
                    ]
                ],
                'ViewerClient' => [
                    'single' => true,
                    'class' => Client::class,
                    'arguments' => [
                        'options' => array_merge($httpDefaultRuntimeOptions, $this->httpOptions['viewer'])
                    ]
                ],
                // Services
                MediaService::class => [
                    'single' => true,
                    'class' => MediaService::class,
                    'arguments' => [
                        'client' => \mr_srv_arg('MediaClient')
                    ]
                ],
                AccountService::class => [
                    'single' => true,
                    'class' => AccountService::class,
                    'arguments' => [
                        'client' => \mr_srv_arg('AccountClient'),
                        'options' => []
                    ]
                ],
                StorageService::class => [
                    'single' => true,
                    'class' => StorageService::class,
                    'arguments' => [
                        'client' => \mr_srv_arg('StorageClient'),
                        'options' => []
                    ]
                ],
                ViewerService::class => [
                    'single' => true,
                    'class' => ViewerService::class,
                    'arguments' => [
                        'client' => \mr_srv_arg('ViewerClient'),
                        'options' => []
                    ]
                ],
                // Repositories
                MediaRepository::class => [
                    'single' => true,
                    'class' => MediaRepository::class,
                    'arguments' => [
                        'client' => \mr_srv_arg('MediaClient'),
                        'options' => []
                    ]
                ],
                UserRepository::class => [
                    'single' => true,
                    'class' => UserRepository::class,
                    'arguments' => [
                        'client' => \mr_srv_arg('AccountClient'),
                        'options' => []
                    ]
                ],
                VendorRepository::class => [
                    'single' => true,
                    'class' => VendorRepository::class,
                    'arguments' => [
                        'client' => \mr_srv_arg('AccountClient'),
                        'options' => []
                    ]
                ],
                OAuthTokenRepository::class => [
                    'single' => true,
                    'class' => OAuthTokenRepository::class,
                    'arguments' => [
                        'client' => \mr_srv_arg('AccountClient'),
                        'options' => []
                    ]
                ],
                CredentialRepository::class => [
                    'single' => true,
                    'class' => CredentialRepository::class,
                    'arguments' => [
                        'client' => \mr_srv_arg('AccountClient'),
                        'options' => []
                    ]
                ],
                FtpFileRepository::class => [
                    'single' => true,
                    'class' => FtpFileRepository::class,
                    'arguments' => [
                        'client' => \mr_srv_arg('StorageClient'),
                        'options' => []
                    ]
                ],
                ViewerRepository::class => [
                    'single' => true,
                    'class' => ViewerRepository::class,
                    'arguments' => [
                        'client' => \mr_srv_arg('ViewerClient'),
                        'options' => []
                    ]
                ],
                // Models
                Media::class => [
                    'single' => false,
                    'class' => Media::class,
                    'arguments' => [
                        'repository' => \mr_srv_arg(UserRepository::class),
                        'data' => null
                    ]
                ],
                User::class => [
                    'single' => false,
                    'class' => User::class,
                    'arguments' => [
                        'repository' => \mr_srv_arg(UserRepository::class),
                        'data' => null
                    ]
                ],
                Vendor::class => [
                    'single' => false,
                    'class' => Vendor::class,
                    'arguments' => [
                        'repository' => \mr_srv_arg(VendorRepository::class),
                        'data' => null
                    ]
                ],
                OAuthToken::class => [
                    'single' => false,
                    'class' => OAuthToken::class,
                    'arguments' => [
                        'repository' => \mr_srv_arg(OAuthTokenRepository::class),
                        'data' => null
                    ]
                ],
                Credential::class => [
                    'single' => false,
                    'class' => Credential::class,
                    'arguments' => [
                        'repository' => \mr_srv_arg(CredentialRepository::class),
                        'data' => null
                    ]
                ],
                FtpFile::class => [
                    'single' => false,
                    'class' => FtpFile::class,
                    'arguments' => [
                        'repository' => \mr_srv_arg(FtpFileRepository::class),
                        'data' => null
                    ]
                ],
                Viewer::class => [
                    'single' => false,
                    'class' => Viewer::class,
                    'arguments' => [
                        'repository' => \mr_srv_arg(ViewerRepository::class),
                        'data' => null
                    ]
                ],
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
            $data = $client->postData("users/authenticate", [
                'vendor_uuid' => $this->accountId,
                'username' => $this->appId,
                'password' => $this->appSecret
            ]);
        } catch (RequestException $ex) {
            // Just avoid request exception from propagating
            if ($this->isDebug()) {
                \mr_logger()->error($ex->getMessage());
            }
        }

        if (! isset($data, $data['data'], $data['data']['token'])) {
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
                TokenAuthMiddleware::AUTH_HEADER => "Bearer {$this->token}"
            ]);

        $client = new Client($httpOptions);
        $data = null;

        try {
            $data = $client->postData("users/$userId/impersonate", []);
        } catch (RequestException $ex) {
            // Just avoid request exception from propagating
            if ($this->isDebug()) {
                \mr_logger()->error($ex->getMessage());
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

    public static function setCredentials($accountId, $appId, $appSecret, array $options = [], array $httpOptions = [])
    {
        self::create($accountId, $appId, $appSecret, null, $options, $httpOptions);
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

    protected function _getAccountId()
    {
        return $this->accountId;
    }

    protected function _getToken()
    {
        return $this->token;
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

    /**
     * @return StorageService
     */
    protected function _getStorageService()
    {
        return $this->_get(StorageService::class);
    }

    /**
     * @return ViewerService
     */
    protected function _getViewerService()
    {
        return $this->_get(ViewerService::class);
    }
}
