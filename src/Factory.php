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
    protected static $definitions = [];
    protected static $instances = [];

    /**
     * @param $instance
     * @param null $name
     */
    public static function register($instance, $name = null)
    {
        if (is_null($name)) {
            $name = get_class($instance);
        }

        if (static::has($name)) {
            throw new \RuntimeException('Duplicated name');
        }

        static::$instances[$name] = $instance;
    }

    public static function define($name, $class, array $dependencies)
    {
        static::$definitions[$name] = [$class, $dependencies];
    }

    public static function has($name)
    {
        return array_key_exists($name, static::$instances);
    }

    protected static function create($name, array $args)
    {
        if (!isset(static::$definitions[$name])) {
            return new $name();
        }

        list($class, $dependencies) = static::$definitions[$name];
        $finalArgs = [];

        foreach ($dependencies as $argName => $argType) {
            if (array_key_exists($argName, $args)) {
                $finalArgs[] = $args[$argName];
            }

            if ($name == $argType) {
                throw new \RuntimeException('Cyclic dependency detected');
            }

            $finalArgs[] = static::get($argType);
        }

        $class = new \ReflectionClass($class);

        return $class->newInstanceArgs($finalArgs);
    }

    public static function get($name)
    {
        if (!static::has($name)) {
            return null;
        }

        return static::$instances[$name];
    }

    public static function resolve($name, array $args = [])
    {
        if (!static::has($name)) {
            $instance = static::create($name, $args);
            static::register($instance, $name);
        }

        return static::get($name);
    }
}