<?php
/**
 * Created by PhpStorm.
 * User: michel
 * Date: 7/27/17
 * Time: 9:28 AM
 */

namespace Mr\Sdk;


class Model
{
    protected $data = [];
    protected $repository;
    protected $fetched = false;
    protected $isModified;


    public function __construct(Repository $repository, array $data = [])
    {
        $this->repository = $repository;
        $this->data = $data;
    }

    public function fetch($force = false)
    {
        if ($this->id) {
            throw new \RuntimeException('Id required');
        }

        if (!$force && $this->fetched) {
            return false;
        }

        $this->data = $this->repository->get($this->id, true);

        return true;
    }

    public function has($name)
    {
        return isset($this->data[$name]);
    }

    public function __isset($name)
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * <b>Magic method</b>. Returns value of specified property
     *
     * @param string $name property name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (!$this->has($name)) {
            return null;
        }

        return $this->data[$name];
    }

    /**
     * <b>Magic method</b>. Sets value of a dynamic property
     *
     * @param string $name property name
     * @param mixed  $value new value
     *
     * @return mixed param value
     */
    public function __set($name, $value)
    {
        $oldValue = $this->{$name};

        if ($modified = $oldValue !== $value) {
            $this->data[$name] = $value;
        }

        $this->isModified = $this->isModified || $modified;
    }

    public function isModified()
    {
        return $this->isModified;
    }

    public function toArray()
    {
        return $this->data;
    }

    public function save()
    {
        $this->repository->persist($this->data);
    }
}