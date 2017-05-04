<?php

namespace Mg\JmesPathServer\Util;


class ExceptionUtil
{

    /**
     * @param \Exception $exception
     * @param bool $isDebugEnabled
     *
     * @return array
     */
    public static function exceptionAsArray(
        \Throwable $exception,
        $isDebugEnabled
    )
    {
        $recursionLevel = 0;
        $recursionLevelMax = 5;

        $isDebugEnabled = ($isDebugEnabled === true);

        $error = self::exceptionAsArrayRecursive(
            $exception,
            $isDebugEnabled,
            $recursionLevel,
            $recursionLevelMax
        );
        if (!is_array($error)) {
            $error = array(
                'message' => '' . __METHOD__ . 'failed!',
            );
        }

        return $error;
    }

    /**
     * @param \Exception $exception
     * @param bool $isDebugEnabled
     * @param int $recursionLevel
     * @param int $recursionLevelMax
     *
     * @return array|null
     */
    private static function exceptionAsArrayRecursive(
        \Throwable $exception,
        $isDebugEnabled,
        $recursionLevel,
        $recursionLevelMax
    )
    {
        $result = null;
        if (!is_int($recursionLevel)) {

            return $result;
        }
        if (!is_int($recursionLevelMax)) {

            return $result;
        }

        if (($recursionLevel < 0) || ($recursionLevelMax < 0)) {

            return $result;
        }

        if ($recursionLevel > $recursionLevelMax) {

            return $result;
        }

        $isDebugEnabled = ($isDebugEnabled === true);
        $recursionLevel++;

        $debug = array(
            'class' => self::getClassnameNice($exception),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'stackTrace' => $exception->getTraceAsString(),
        );

        $error = array(
            'class' => self::getClassnameNice($exception),
            'message' => $exception->getMessage(),
            'data' => null,
            'debug' => $debug,
        );

        if (!$isDebugEnabled) {
            $error['debug'] = null;
        }

        $result = array();
        foreach ($error as $key => $value) {
            if ($value instanceof \Exception) {

                $value = self::exceptionAsArrayRecursive(
                    $value,
                    $isDebugEnabled,
                    $recursionLevel,
                    $recursionLevelMax
                );
                if (is_array($value)) {
                    $value = self::ensureAssocArrayIsJsonSerializable($value);
                }
            }

            $result[$key] = $value;
        }

        $result = self::ensureAssocArrayIsJsonSerializable($result);

        return $result;
    }

    /**
     * @param object|string $instance
     *
     * @return string
     */
    private static function getClassnameNice($instance)
    {
        $result = 'null';

        $className = null;
        if (is_string($instance)) {
            $className = $instance;
        }

        if (is_object($instance)) {

            try {
                $className = get_class($instance);
            } catch (\Exception $e) {
                //NOP
            }
        }

        if (!is_string($className)) {

            return $result;
        }

        if (empty($className)) {

            return $result;
        }

        $classNameNice = str_replace(
            array('_', '\\'),
            '.',
            $className
        );

        return $classNameNice;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    private static function ensureAssocArrayIsJsonSerializable($array)
    {
        $result = array();
        if (!is_array($array)) {

            return $result;
        }

        foreach ($array as $key => $value) {
            if (is_string(self::jsonEncode($value, false))) {
                $result[$key] = $value;
            } else {
                // could not serialize as json
                $result[$key] = null;
            }
        }

        return $result;
    }

    /**
     * @param mixed $value
     * @param bool $marshallExceptions
     *
     * @return null|string
     * @throws \Exception
     */
    private static function jsonEncode(
        $value,
        $marshallExceptions
    )
    {
        $marshallExceptions = ($marshallExceptions === true);

        $result = null;
        try {
            $result = json_encode($value);
        } catch (\Exception $e) {
            $result = null;
            if ($marshallExceptions) {

                // delegate exception
                throw $e;
            }
        }

        if (!is_string($result)) {
            $result = null;
        }

        return $result;

    }

}