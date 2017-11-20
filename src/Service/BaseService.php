<?php

namespace Mr\Sdk\Service;


use Mr\Sdk\Http\Client;
use Mr\Sdk\Interfaces\ContainerAccessorInterface;
use Mr\Sdk\Traits\ContainerAccessor;

abstract class BaseService implements ContainerAccessorInterface
{
    use ContainerAccessor;

    /**
     * @var Client
     */
    protected $client;
    protected $options;

    /**
     * Service constructor.
     * @param Client $client
     * @param array $options
     */
    public function __construct(Client $client, array $options = [])
    {
        $this->client = $client;
        $this->options = $options;
    }

    public function getClient()
    {
        return $this->client;
    }
}