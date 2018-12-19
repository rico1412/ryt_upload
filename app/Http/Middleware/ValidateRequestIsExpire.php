<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\AppException;

/**
 * 判断请求是否已过期
 *
 * @author 51004
 */
class ValidateRequestIsExpire
{
    /**
     * 请求有效时间 / 分钟
     *
     * @var int
     */
    protected $delay = 2;

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
        $requestAt = $request->get('_ts');

        if (! $requestAt || $this->isExpire($requestAt))
        {
            throw new AppException(100010);
        }

        return $next($request);
    }

    /**
     * 判断是否已过期
     *
     * @param $requestAt
     * @return bool
     */
    protected function isExpire($requestAt)
    {
        if (! ($diff = get_now() - $requestAt)) return false;

        return $diff > (60 * $this->delay);
    }
}