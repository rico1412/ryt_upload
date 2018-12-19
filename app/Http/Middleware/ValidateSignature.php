<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\AppException;

/**
 * 请求加密校验
 *
 * @author 51004
 */
class ValidateSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @throws AppException
     */
    public function handle($request, Closure $next)
    {
        if (config('service.validation_sign'))
        {
            if (! app('Encryption\Dictionary')->validate(config('service.sign_key'), $request->all()))
            {
                throw new AppException(100009);
            }
        }

        // 移除 _sign 参数, 很重要
        $request->offsetUnset('_sign');

        return $next($request);
    }
}