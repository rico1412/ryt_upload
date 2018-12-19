<?php

namespace App\Providers;

use Symfony\Component\Finder\Finder;
use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
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
        $this->loadConfig();
    }

    /**
     * Load config file.
     */
    public function loadConfig()
    {
        $files = Finder::create()->files()->name('*.php')->in($this->app->basePath('config'));

        foreach ($files as $file)
        {
            $filename = $file->getFileName();

            if($place = strpos($filename,'.php'))
            {
                $filename = mb_substr($filename,0,$place);

                $this->setConfig($filename, $file->getRealPath(), $file->getRelativePath() ?: '');
            }
        }
    }

    /**
     * 设置配置档，可添加前缀
     *
     * @param $name
     * @param $path
     * @param string $prefix
     */
    protected function setConfig($name, $path, $prefix = '')
    {
        if ($prefix)
        {
            $prefix = strtr($prefix, DIRECTORY_SEPARATOR, '.');
            $name   = $prefix . '.' . $name;
        }

        config([$name => require $path]);
    }
}
