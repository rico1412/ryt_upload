<?php

namespace App\Kernel\Traits;

/**
 * 接口响应
 *
 * @author 51004
 */
trait ApiResponseTrait
{
    /**
     * api 响应
     *
     * @param array $data
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function response(array $data = [], int $status = 200, array $headers = [], int $options = JSON_PRETTY_PRINT)
    {
        return response()->json($data, $status, $headers, $options);
    }

    /**
     * @param array $data
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function ok(array $data, array $headers = [], int $options = JSON_PRETTY_PRINT)
    {
        return $this->response($data, 200, $headers, $options);
    }

    /**
     * @param $data
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function created($data, array $headers = [], int $options = JSON_PRETTY_PRINT)
    {
        return $this->response($data, 201, $headers, $options);
    }

    /**
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function noContent(array $headers = [], int $options = JSON_PRETTY_PRINT)
    {
        return $this->response([], 204, $headers, $options);
    }

    /**
     * @param string $message
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function unauthorized($message = '401_Access_Deny', array $headers = [], int $options = 0)
    {
        return $this->response(compact('message'), 401, $headers, $options);
    }

    /**
     * @param string $message
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function notFound($message = '404_Not_Found', array $headers = [], int $options = 0)
    {
        return $this->response(compact('message'), 404, $headers, $options);
    }

    /**
     * @param string $message
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function methodNotAllowed($message = '405_Method_Not_Allowed', array $headers = [], int $options = 0)
    {
        return $this->response(compact('message'), 404, $headers, $options);
    }

    /**
     * @param array $data
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function error(array $data, array $headers = [], int $options = 0)
    {
        return $this->response($data, 500, $headers, $options);
    }
}