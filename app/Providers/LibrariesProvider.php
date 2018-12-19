<?php

namespace App\Providers;

use App\Libraries\Encryption\Dictionary;
use Illuminate\Support\ServiceProvider;

class LibrariesProvider extends ServiceProvider
{
    
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
    
    public function boot()
    {
        $this->app->singleton('Encryption\Dictionary', function(){
            return new Dictionary();
        });
    }
    
    
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'Encryption\Dictionary',
        ];
    }
    
}
