<?php

namespace Mg\JmesPathServer\Util;


class ArrayAssocUtil
{

    /**
     * @param array $data
     * @param \Closure $filter
     * @return array
     */
    public static function filterKeys($data, \Closure $filter)
    {
        if (!is_array($data)) {

            return [];
        }

        $result = [];
        foreach ($data as $key => $value) {

            $isMatched = call_user_func_array($filter, [$key, $value]);
            if ($isMatched === true) {

                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * @param $data
     * @param $key
     * @param \Closure $mapper
     * @return mixed
     */
    public static function getKeyAndMapValue($data, $key, \Closure $mapper)
    {
        $value = static::getKeyOrDefault($data, $key, null);

        return call_user_func_array($mapper, [$value]);
    }

    /**
     * @param array $data
     * @param string|int|float $key
     * @param mixed $default
     * @return mixed
     */
    public static function getKeyOrDefault($data, $key, $default)
    {
        if (!is_array($data)) {

            return $default;
        }

        if (!is_scalar($key)) {

            return $default;
        }

        if (array_key_exists($key, $data)) {

            return $data[$key];
        }

        return $default;
    }

    /**
     * @param array $data
     * @param \Closure $predicate
     * @return array
     */
    public static function filter(array $data, \Closure $predicate)
    {
        $result = [];
        foreach ($data as $key => $value) {
            $keep = call_user_func_array($predicate, [$value, $key]);
            if ($keep) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * @param array $data
     * @param \Closure $mapper
     * @return array
     */
    public static function map(array $data, \Closure $mapper)
    {
        $result = [];
        foreach ($data as $key => $value) {
            $result[$key] = call_user_func_array($mapper, [$value, $key]);
        }

        return $result;
    }

    /**
     * @param array $data
     * @param \Closure $mapper
     * @return array
     */
    public static function mapKeys(array $data, \Closure $mapper)
    {
        $result = [];
        foreach ($data as $key => $value) {
            $mappedKey = call_user_func_array($mapper, [$value, $key]);
            $result[$mappedKey] = $value;
        }

        return $result;
    }

    /**
     * @param array $data
     * @param \Closure $mapper
     * @return array
     */
    public static function mapAndFilterKeys(array $data, \Closure $mapper, \Closure $filter)
    {
        $result = [];
        foreach ($data as $sourceKey => $value) {
            $mappedKey = call_user_func_array($mapper, [$value, $sourceKey]);
            $keepItem = call_user_func_array($filter, [$value, $mappedKey, $sourceKey]);
            if ($keepItem) {
                $result[$mappedKey] = $value;
            }
        }

        return $result;
    }

    /**
     * @param array $data
     * @param \Closure $consumer
     * @return array
     */
    public static function flatMap(array $data, \Closure $consumer)
    {

        $resultList = array();
        foreach ($data as $key => $value) {
            $mappedList = call_user_func_array($consumer, [$value]);
            if (!is_array($mappedList)) {

                continue;
            }
            foreach ($mappedList as $mappedListItemValue) {
                $resultList[] = $mappedListItemValue;
            }

        }

        return $resultList;
    }

    /**
     * @param array $data
     * @param \Closure $groupKeyEmitter
     * @return array
     */
    public static function groupValues(array $data, \Closure $groupKeyEmitter)
    {
        $resultDict = [];

        foreach ($data as $key => $value) {
            $groupKey = call_user_func_array($groupKeyEmitter, [$value, $key]);

            $group = static::getKeyOrDefault($resultDict, $groupKey, null);
            if (!is_array($group)) {
                $group = [];
            }
            $group[] = $value;
            $resultDict[$groupKey] = $group;
        }

        return $resultDict;
    }

    /**
     * @param $array
     * @param int $index
     * @param $defaultValue
     * @return mixed
     */
    public static function getValueAtIndexOrElse($array, $index, $defaultValue)
    {
        if (!is_array($array)) {

            return $defaultValue;
        }
        $i = -1;
        foreach ($array as $value) {
            $i++;
            if ($i === $index) {

                return $value;
            }
        }

        return $defaultValue;
    }

    /**
     * @param $array
     * @param $identityValue
     * @param \Closure $accumulator
     * @return mixed
     */
    public static function reduce($array, $identityValue, \Closure $accumulator)
    {

        if (!is_array($array)) {
            $array = [];
        }

        $accumulatedValue = $identityValue;
        foreach ($array as $key => $value) {
            $accumulatedValue = call_user_func_array($accumulator, [$accumulatedValue, $value, $key]);
        }

        return $accumulatedValue;
    }


}