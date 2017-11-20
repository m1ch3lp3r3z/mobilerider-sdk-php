<?php

namespace Mr\Sdk\Model;


use Mr\Sdk\Interfaces\ContainerAccessorInterface;
use Mr\Sdk\Repository\BaseRepository;
use Mr\Sdk\Traits\ContainerAccessor;

/**
 * @property mixed id
 */
abstract class BaseModel implements ContainerAccessorInterface
{
    use ContainerAccessor;

    protected $repository;
    protected $data;
    protected $fetched = false;
    protected $isModified = false;
    protected $isDeleted = false;

    public function __construct(BaseRepository $repository, array $data = [])
    {
        $this->repository = $repository;
        $this->data = $data;
    }

    /**
     * METHODS TO BE OVERRIDDEN BY CHILD CLASS
     */

    public static function getResource()
    {
        throw new \RuntimeException('To be defined by child class');
    }

    /**
     * @return bool
     */
    public function isNew()
    {
        return !$this->id;
    }

    protected function ensureNotNew()
    {
        if ($this->isNew()) {
            throw new \RuntimeException('Save this model first');
        }
    }

    public function getUri()
    {
        $arr = [static::getResource()];

        if (!$this->isNew()) {
            $arr[] = $this->id;
        }

        return implode('/', $arr);
    }

    public function fetch($force = false)
    {
        if (!$this->id) {
            throw new \RuntimeException('Id required');
        }

        if (!$force && $this->fetched) {
            return false;
        }

        $this->data = $this->repository->getData($this->id);

        return true;
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
        if (!isset($this->$name)) {
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

    public function __debugInfo()
    {
        return $this->data;
    }

    public function fill(array $data)
    {
        $this->data = array_merge($this->data, $data);
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
        $this->repository->persist($this);
        $this->isModified = false;
    }

    public function delete()
    {
        $this->repository->delete($this);
        $this->isDeleted = true;
    }
}