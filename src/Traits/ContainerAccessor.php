<?php

namespace Mr\Sdk\Traits;

use Mr\Sdk\Container;

trait ContainerAccessor
{
    /**
     * @var Container
     */
    protected $container;

    protected function _get($name, array $args = [])
    {
        return $this->container->get($name, $args);
    }

    /**
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}