<?php

namespace App\Kernel\Base;

use Exception;
use Zttp\{Zttp};
use App\Exceptions\{
    AppException, RuntimeException, ApiException
};
use GuzzleHttp\{
    Exception\ClientException, Exception\ConnectException, Exception\RequestException
};

/**
 * 接口请求
 *
 * @author 51004
 */
abstract class BaseApi
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var array
     */
    protected $apiList;

    /**
     * @var string
     */
    protected $module;

    /**
     * @var array
     */
    protected $config;

    /**
     * BaseApi constructor.
     */
    public function __construct()
    {
        $this->config = $this->getConfig();

        $this->init();
    }

    /**
     * @return string
     */
    protected function signature(): string
    {
        return $this->config['sign_key'];
    }

    /**
     * @return bool
     */
    protected abstract function authorize(): bool;

    /**
     * 初始化
     *
     * @return void
     */
    protected function init()
    {
        $this->setHost($this->config['host']);

        $this->setApiList($this->config['api'][$this->config['version']]);
    }

    /**
     * Send get request
     *
     * @param string $api
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws AppException
     * @throws RuntimeException
     * @throws ApiException
     */
    public function get(string $api, array $params = [], array $headers = [])
    {
        return $this->request('GET', $api, $params, $headers);
    }

    /**
     * Send post request
     *
     * @param string $api
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws AppException
     * @throws RuntimeException
     * @throws ApiException
     */
    public function post(string $api, array $params = [], array $headers = [])
    {
        return $this->request('POST', $api, $params, $headers);
    }

    /**
     * Send delete request
     *
     * @param string $api
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws AppException
     * @throws RuntimeException
     * @throws ApiException
     */
    public function delete(string $api, array $params = [], array $headers = [])
    {
        return $this->request('DELETE', $api, $params, $headers);
    }

    /**
     * Send put request
     *
     * @param string $api
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws AppException
     * @throws ApiException
     * @throws RuntimeException
     */
    public function put(string $api, array $params = [], array $headers = [])
    {
        return $this->request('PUT', $api, $params, $headers);
    }

    /**
     * Send patch request
     *
     * @param string $api
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws AppException
     * @throws RuntimeException
     * @throws ApiException
     */
    public function patch(string $api, array $params = [], array $headers = [])
    {
        return $this->request('PATCH', $api, $params, $headers);
    }

    /**
     * Handle request
     *
     * @param string $method
     * @param string $api
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws AppException
     * @throws RuntimeException
     * @throws ApiException
     */
    private function request(string $method, string $api, array $params = [], array $headers = [])
    {
        $params = $this->addSignParams($params);

        try
        {
            $response = $this->sendRequest($method, $api, $params, $headers);
        }
        catch(ClientException $e)
        {
            throw new AppException(1100001, [
                'code' => $e->getCode(), 'message' => $e->getMessage()
            ]);
        }
        catch (ConnectException $e)
        {
            throw new AppException(110002, [
                'code' => $e->getCode(), 'message' => $e->getMessage()
            ]);
        }
        catch (RequestException $e)
        {
            throw new AppException(110003, [
                'code' => $e->getCode(), 'message' => $e->getMessage()
            ]);
        }
        catch (Exception $e)
        {
            throw new RuntimeException(100000, [
                'code' => $e->getCode(), 'message' => $e->getMessage()
            ]);
        }

        return $this->parseResponse($response);
    }

    /**
     * Send Request
     *
     * @param string $method
     * @param string $api
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws AppException
     */
    protected function sendRequest(string $method, string $api, array $params, array $headers = [])
    {
        $url = $this->getUrl($api);

        if (config('app.env') == 'local' || config('app.debug') == true)
        {
            info($url);
        }

        return $headers ?
            Zttp::withHeaders($headers)->$method($url, $params) : Zttp::$method($url, $params);
    }
    
    /**
     * Add request sign params
     *
     * @param array $params
     * @return array
     * @throws AppException
     */
    protected function addSignParams(array $params = [])
    {
        if (! $this->authorize()) return $params;

        if (empty($this->signature())) throw new AppException(110005);

        $params['_ts']       = get_now();
        $params['_rd']       = str_random(8);
        $params['_terminal'] = config('service.name');
        $params['_sign']     = app('Encryption\Dictionary')->encrypt($params, $this->signature());

        return $params;
    }

    /**
     * Parse response data.
     *
     * @param $response
     * @return mixed
     * @throws ApiException
     * @throws AppException
     * @throws RuntimeException
     */
    protected function parseResponse($response)
    {
        $content = json_strict_decode($response->body());

        $this->handleException($response->getStatusCode(), $content);

        return $content['data'];
    }

    /**
     * Handle response exception.
     *
     * @param $code
     * @param $content
     * @throws ApiException
     * @throws AppException
     * @throws RuntimeException
     */
    protected function handleException($code, $content)
    {
        if ($code > 299 || $code < 199)
        {
            if (config('app.env') == 'local' || config('app.debug') == true)
            {
                throw new AppException(110002, $content);
            }

            throw new AppException(110002);
        }

        if (! isset($content['code']) || ! array_key_exists('data', $content))
        {
            throw new AppException(110003);
        }

        if ($content['code'])
        {
            throw new ApiException($content);
        }
    }

    /**
     * Get request url.
     *
     * @param string $api
     * @return string
     * @throws AppException
     */
    public function getUrl(string $api): string
    {
        return $this->getHost() . '/' . $this->getApi($api);
    }

    /**
     * Get request host
     *
     * @return string
     * @throws AppException
     */
    public function getHost(): string
    {
        if (! $this->host) throw new AppException(110004);

        return $this->host;
    }

    /**
     * Set host
     *
     * @param mixed $host
     */
    public function setHost($host): void
    {
        $this->host = $host;
    }

    /**
     * Get config
     *
     * @return mixed
     */
    protected function getConfig()
    {
        return config('api.' . $this->module);
    }

    /**
     * Get api list
     *
     * @return array
     */
    public function getApiList(): array
    {
        return $this->apiList;
    }

    /**
     * Set api list
     *
     * @param array $apiList
     */
    public function setApiList(array $apiList): void
    {
        $this->apiList = $apiList;
    }

    /**
     * Get api uri
     *
     * @param $name
     * @return mixed
     * @throws AppException
     */
    public function getApi($name)
    {
        if ($api = array_get($this->apiList, $name))
        {
            return $api;
        }

        throw new AppException(110000);
    }
}