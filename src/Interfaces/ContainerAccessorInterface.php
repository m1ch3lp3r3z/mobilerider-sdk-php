<?php

namespace Mr\Sdk\Interfaces;


use Mr\Sdk\Container;

interface ContainerAccessorInterface
{
    public function setContainer(Container $container);
    public function getContainer();
}