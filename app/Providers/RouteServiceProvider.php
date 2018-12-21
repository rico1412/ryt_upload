<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $routes = [
        'api' => [
            'v1' => [
                'domain'     => 'domain.api',
                'prefix'     => '/v1',
                'namespace'  => 'App\Http\Controllers\Api\V1',
                'files'      => [
//                    'routes/api/v1/question.php',
                ],
                'middleware' => ['request.expired', 'signed'],
            ],
        ],
        'common' => [
            'publics' => [
                'domain'    => '*',
                'prefix'    => '',
                'namespace' => 'App\Http\Controllers\Common',
                'files'     => [
                    'routes/common/publics.php',
                ],
                'middleware' => [],
            ]
        ],
        'admin' => [
            'common' => [
                'domain' => 'domain.admin',       //config('domain.example')
                'prefix' => '/admin',
                'namespace' => 'App\Http\Controllers\Admin',
                'files' => [
//                    'routes/api/admin/question.php',
                ],
            ],
        ],
    ];

    public function boot()
    {
        $this->mapRoutes();
    }

    protected function mapRoutes()
    {
        $domain = head(explode(':', get_http_host()));

        foreach ($this->routes as $name => $version)
        {
            foreach ($version as $route)
            {
                if ($route['domain'] != '*' && $domain != config($route['domain'])) continue;

                $this->loadRoutes($route);
            }
        }
    }

    protected function loadRoutes($route)
    {
        foreach ($route['files'] as $file)
        {
            $this->app->router->group(
                array_only($route, ['namespace', 'prefix', 'middleware']),
                function ($router) use ($file)
                {
                    require base_path($file);
                }
            );
        }
    }
}
