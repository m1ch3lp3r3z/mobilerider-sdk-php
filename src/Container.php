<?php

namespace Mr\Sdk;


use Mr\Sdk\Interfaces\ContainerAccessorInterface;

class Container
{
    protected $definitions;
    protected $services = [];

    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
        $this->services['container'] = $this;
    }

    public function get($name, array $args = [])
    {
        if (!isset($this->services[$name])) {
            if (!isset($this->definitions[$name])) {
                return null;
            }

            $definition = $this->definitions[$name];

            if (is_object($definition)) {
                $service = $definition;
                $keep = true;
            } else {
                $service = $this->create($definition, $args);
                $keep = isset($definition['single']) && $definition['single'] === true;
            }

            if ($keep) {
                $this->services[$name] = $service;
            }

            return $service;
        }

        return $this->services[$name];
    }

    protected function create(array $definition, array $args)
    {
        $arguments = [];

        foreach ($definition['arguments'] as $key => $argument) {
            if (array_key_exists($key, $args)) {
                $arguments[] = $args[$key];
                continue;
            }

            if (is_null($argument)) {
                continue;
            }

            if (is_string($argument)) {
                $dep = $this->get($argument);

                if (!is_null($dep)) {
                    $arguments[] = $dep;
                    continue;
                }
            }

            $arguments[] = $argument;
        }

        $service = Factory::create($definition['class'], $arguments);

        // Tried class_uses but it does not seem to retrieve traits
        // used by parent classes
        if ($service instanceof ContainerAccessorInterface) {
            $service->setContainer($this);
        }

        return $service;
    }
}
