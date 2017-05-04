<?php

namespace Mg\JmesPathServer\Definition;


class ArrayList
{
    protected $data = array();

    /**
     * ArrayList constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = array_values($data);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * @return ArrayList
     */
    public function toList()
    {
        return new ArrayList($this->data);
    }
}