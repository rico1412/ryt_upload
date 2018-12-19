<?php

namespace App\Exceptions;

use Exception;
use App\Kernel\Traits\ApiResponseTrait;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\{
    HttpException,
    NotFoundHttpException,
    MethodNotAllowedHttpException
};

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        ValidationException::class,
        MethodNotAllowedHttpException::class,
        NotFoundHttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * @var array
     */
    protected $handles = [
        RuntimeException::class              => 'handelRuntimeException',
        HttpException::class                 => 'handelHttpException',
        ModelNotFoundException::class        => 'handleModelNotFoundException',
        ValidationException::class           => 'handleValidationException',
        AuthorizationException::class        => 'handleAuthorizationException',
        AppException::class                  => 'handleAppException',
        ApiException::class                  => 'handleApiException',
        NotFoundHttpException::class         => 'handleNotFoundHttpException',
        ArticleException::class              => 'handleArticleException',
        MethodNotAllowedHttpException::class => 'handleMethodNotAllowedHttpException',
        ValidatorException::class            => 'handleValidatorException',
        FaqInfoException::class              => 'handleFaqInfoException',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Exception $e
     * @throws Exception
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Exception $e
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function render($request, Exception $exception)
    {
        foreach ($this->handles as $class => $func)
        {
            if (get_class($exception) == $class)
            {
                return $this->$func($exception, $request);
            }
        }

        return $this->handelHttpException($exception, $request);
    }

    /**
     * Handel runtime exception response.
     *
     * @param \App\Exceptions\RuntimeException $exception
     * @param $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function handelRuntimeException(RuntimeException $exception, $request)
    {
        $data = (new AppException(100002))->all();

        return $this->error($data);
    }

    /**
     * handel http exception response
     *
     * @param $exception
     * @param $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function handelHttpException($exception, $request)
    {
        $data = (new AppException(100000))->all();

        return $this->error($data);
    }

    /**
     * Handle model not found exception response
     *
     * @param ModelNotFoundException $exception
     * @param $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function handleModelNotFoundException(ModelNotFoundException $exception, $request)
    {
        $data = (new AppException(100000))->all();

        return $this->error($data);
    }

    /**
     * Handle validation exception response
     *
     * @param ValidationException $exception
     * @param $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function handleValidationException(ValidationException $exception, $request)
    {
        $data = (new AppException(100003, $exception->errors()))->all();

        return $this->ok($data);
    }

    /**
     * Handle app exception response
     *
     * @param AppException $exception
     * @param $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function handleAppException(AppException $exception, $request)
    {
        return $this->ok($exception->all());
    }

    /**
     * Handle api exception response
     *
     * @param ApiException $exception
     * @param $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function handleApiException(ApiException $exception, $request)
    {
        return $this->ok($exception->getData());
    }

    /**
     * Handle not found http exception response
     *
     * @param NotFoundHttpException $exception
     * @param $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function handleNotFoundHttpException(NotFoundHttpException $exception, $request)
    {
        $data = (new AppException(100001))->all();

        return $this->error($data);
    }

    /**
     * Handle article exception response
     *
     * @param ArticleException $exception
     * @param $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function handleArticleException(ArticleException $exception, $request)
    {
        return $this->ok($exception->all());
    }

    /**
     * Handle method not allowed http exception response
     *
     * @param MethodNotAllowedHttpException $exception
     * @param $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function handleMethodNotAllowedHttpException(MethodNotAllowedHttpException $exception, $request)
    {
        $data = (new AppException(100007))->all();

        return $this->ok($data);
    }

    protected function handleValidatorException(ValidatorException $exception, $request)
    {
        $data = (new AppException(100003, $exception->getMessageBag()->toArray()))->all();

        return $this->ok($data);
    }

    /**
     * @author 秦昊
     * Date: 2018/8/31 18:18
     * @param FaqInfoException $exception
     * @param $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function handleFaqInfoException(FaqInfoException $exception, $request)
    {
        return $this->ok($exception->all());
    }
}
