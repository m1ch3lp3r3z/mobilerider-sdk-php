<?php

namespace Mr\Sdk;


class Factory
{
    public static function create($class, $arguments = [])
    {
        if (!$arguments) {
            return new $class();
        }

        $class = new \ReflectionClass($class);

        return $class->newInstanceArgs($arguments);
    }
}