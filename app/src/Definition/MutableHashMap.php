<?php

namespace Mg\JmesPathServer\Definition;


class MutableHashMap extends HashMap
{
    /**
     * @return MutableHashMap
     */
    function toMutableHashMap()
    {
        return new MutableHashMap($this->data);
    }

    /**
     * @param $key
     * @param $value
     * @return MutableHashMap
     */
    function with($key, $value)
    {
        $newInstance = new self($this->data);
        $newInstance->put($key, $value);

        return $newInstance;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    function put($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

}