<?php

namespace App\Exceptions;

use Exception;

/**
 * 接口响应异常
 *
 * @author 51004
 */
class ApiException extends Exception
{
    /**
     * @var array
     */
    protected $data;
    
    /**
     * ApiException constructor.
     * @param $data
     */
    public function __construct(array $data = [])
    {
        if(!isset($data['message']) || !isset($data['code'])){
            throw new RuntimeException(100001);
        }
        
        $this->data = $data;
        
        parent::__construct($data['message'], $data['code']);
    }
    
    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
