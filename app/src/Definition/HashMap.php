<?php

namespace Mg\JmesPathServer\Definition;

use Mg\JmesPathServer\Util\ArrayAssocUtil;

class HashMap
{
    /**
     * @var array
     */
    protected $data = array();

    /**
     * HashMap constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getOrNull($key)
    {
        return ArrayAssocUtil::getKeyOrDefault($this->data, $key, null);
    }

    /**
     * @param string $key
     * @param $defaultValue
     * @return mixed
     */
    public function getOrDefault($key, $defaultValue)
    {
        return ArrayAssocUtil::getKeyOrDefault($this->data, $key, $defaultValue);
    }

    /**
     * @param string $key
     * @param \Closure $supplier
     * @return mixed
     */
    public function getOrElse($key, \Closure $supplier)
    {
        return ArrayAssocUtil::getKeyOrElse($this->data, $key, $supplier);
    }

    /**
     * @return ArrayList
     */
    public function keys()
    {
        return new ArrayList(array_keys($this->data));
    }

    /**
     * @return ArrayList
     */
    public function values()
    {
        return new ArrayList(array_values($this->data));
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->count() < 1;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count(array_keys($this->data));
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * @return HashMap
     */
    public function toMap()
    {
        return new HashMap($this->data);
    }

    /**
     * @return MutableHashMap
     */
    public function toMutableHashMap()
    {
        return new MutableHashMap($this->data);
    }


}