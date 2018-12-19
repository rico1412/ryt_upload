<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * 转换 html 特殊字符
 *
 * @author 51004
 */
class TransformHtmlSpecialChars
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->cleanParameterBag($request->query);

        if ($request->isJson())
        {
            $this->cleanParameterBag($request->json());
        }
        else
        {
            $this->cleanParameterBag($request->request);
        }

        return $next($request);
    }

    /**
     * Clean the data in the given array.
     *
     * @param  array  $data
     * @return array
     */
    protected function cleanArray(array $data)
    {
        return collect($data)->map(function ($value, $key) {
            return $this->cleanValue($key, $value);
        })->all();
    }

    /**
     * Clean the data in the parameter bag.
     *
     * @param ParameterBag $bag
     */
    protected function cleanParameterBag(ParameterBag $bag)
    {
        $bag->replace($this->cleanArray($bag->all()));
    }

    /**
     * Clean the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function cleanValue($key, $value)
    {
        return is_array($value) ?
            $this->cleanArray($value) : $this->transform($key, $value);
    }

    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        return e($value);
    }
}