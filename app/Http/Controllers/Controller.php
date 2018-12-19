<?php

namespace App\Http\Controllers;

use App\Kernel\Traits\ApiResponseTrait;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use ApiResponseTrait;

    /**
     * 接口数据返回
     * User: lizhenhai
     * Date: 2018/7/10 0010
     * @param $data
     * @param string $message
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function revert($data, string $message = 'success!')
    {
        if(is_object($data))
        {
            if(method_exists($data,'toArray'))
            {
                $data = $data->toArray();
            }
        }
        
        if(! is_null($data) && !is_array($data))
        {
            $data = (array)$data;
        }
        
        if(isset($data['code']) && isset($data['data']) && isset($data['module']))
        {
            if ($message) $data['message'] = $message;

            return $data;
        }
        
        $response = [
            'code'    => 0,
            'message' => $message ?: 'success!',
            'data'    => $data,
            'time'    => get_now(),
            'module'  => config('service.name'),
        ];
        
        return $this->ok($response);
    }
}
