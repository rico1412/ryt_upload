<?php

namespace App\Http\Controllers\Common;

use App\Exceptions\AppException;
use App\Exceptions\ArticleException;
use App\Exceptions\RuntimeException;

/**
 *
 *
 * @author 51004
 */
class IndexController extends BaseController
{
    /**
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function now()
    {
        return $this->revert([
            'now'      => get_now(),
            'timezone' => config('app.timezone'),
        ]);
    }

    /**
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function exceptions()
    {
        $items = [
            'AppException'        => AppException::getCodeMaps(),
            'RuntimeException'    => RuntimeException::getCodeMaps(),
            'ArticleException'    => ArticleException::getCodeMaps(),
        ];

        return $this->revert($items);
    }
}