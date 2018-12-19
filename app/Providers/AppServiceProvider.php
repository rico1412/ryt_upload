<?php

namespace App\Providers;

use App\Validator\CharacterLength;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    public function boot()
    {
        app('validator')->extend('character_length', CharacterLength::class . '@length');
    }
}
