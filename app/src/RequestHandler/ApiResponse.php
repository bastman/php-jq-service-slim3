<?php
/**
 * Created by PhpStorm.
 * User: sebastians
 * Date: 04.05.17
 * Time: 15:41
 */

namespace Mg\JmesPathServer\RequestHandler;


class ApiResponse
{
    /** @var int */
    private $statusCode = 200;
    /** @var ?array */
    private $error;
    /** @var ?array */
    private $result;

    /**
     * ApiResponse constructor.
     * @param int $statusCode
     * @param $error
     * @param $data
     */
    public function __construct(int $statusCode, ?array $error, ?array $result)
    {
        $this->statusCode = $statusCode;
        $this->error = $error;
        $this->result = $result;
    }

    public static function ofStatusCode(int $statusCode)
    {
        return new self($statusCode, null, null);
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return bool
     */
    function hasError(): bool
    {
        return is_array($this->error);
    }

    /**
     * @return array|null
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param int $statusCode
     * @return ApiResponse
     */
    public function withStatusCode(int $statusCode): ApiResponse
    {
        return new ApiResponse($statusCode, $this->error, $this->result);
    }

    /**
     * @param array|null $error
     * @return ApiResponse
     */
    public function withError(?array $error): ApiResponse
    {
        return new ApiResponse($this->statusCode, $error, $this->body);
    }

    /**
     * @param array|null $result
     * @return ApiResponse
     */
    public function withResult(?array $result): ApiResponse
    {
        return new ApiResponse($this->statusCode, $this->error, $result);
    }

}