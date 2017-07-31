<?php
/**
 * Created by PhpStorm.
 * User: michel
 * Date: 7/27/17
 * Time: 10:48 AM
 */

namespace Mr\Sdk;


class Factory
{
    protected $definitions;
    protected $instances;

    public function __construct(array $definitions = [], array $instances = [])
    {
        $this->definitions = $definitions;
        $this->instances = ['Factory' => $this] + $instances;
    }

    /**
     * @param $instance
     * @param null $name
     */
    public function register($instance, $name = null)
    {
        if (is_null($name)) {
            $name = get_class($instance);
        }

        if ($this->has($name)) {
            throw new \RuntimeException('Duplicated name');
        }

        $this->instances[$name] = $instance;
    }

    public function replace($instance, $name)
    {
        if (!$this->has($name)) {
            throw new \RuntimeException('Instance not found, use register method instead');
        }

        $this->instances[$name] = $instance;
    }

    public function define($name, $class, array $dependencies)
    {
        $this->definitions[$name] = [$class, $dependencies];
    }

    public function has($name)
    {
        return array_key_exists($name, $this->instances);
    }

    public function create($name, array $args, $strict = false)
    {
        if (!isset($this->definitions[$name])) {
            throw new \RuntimeException("Definition not found for $name");
        }

        list($class, $dependencies) = $this->definitions[$name];
        $finalArgs = [];

        foreach ($dependencies as $argName => $argType) {
            // Check if this argument was provided now
            if (empty($argType) || array_key_exists($argName, $args)) {
                $finalArgs[] = $args[$argName];
                continue;
            }

            if (is_array($argType)) {
                $finalArgs[] = $argType['value'];
                continue;
            }

            if (!is_string($argType)) {
                throw new \RuntimeException('Argument type not supported');
            }

            if ($name == $argType) {
                throw new \RuntimeException('Cyclic dependency detected');
            }

            if ($strict) {
                $finalArgs[] = $this->get($argType);
            } else {
                $finalArgs[] = $this->resolve($argType);
            }
        }

        $class = new \ReflectionClass($class);

        return $class->newInstanceArgs($finalArgs);
    }

    public function get($name, $strict = true)
    {
        if (!$this->has($name)) {
            if ($strict) {
                throw new \RuntimeException('Instance not found for: ' . $name);
            } else {
                return null;
            }
        }

        return $this->instances[$name];
    }

    public function resolve($name, array $args = [])
    {
        if (!$this->has($name)) {
            $instance = $this->create($name, $args);
            $this->register($instance, $name);
        }

        return $this->get($name);
    }

    public function clear($name = null)
    {
        if (is_null($name)) {
            $this->instances = [];
        } else {
            unset($this->instances[$name]);
        }
    }
}