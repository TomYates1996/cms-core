<?php

namespace BaseCms\Providers;

use Illuminate\Support\ServiceProvider;

class CmsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerRoutes();

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'cms');

        $this->publishes([
            __DIR__ . '/../../resources/js' => resource_path('vendor/cms-core/js'),
        ], 'cms-assets');

        $this->publishes([
            __DIR__ . '/../../config' => config_path(),
        ], 'cms-config');
    }

    public function register()
    {
        foreach (glob(__DIR__ . '/../../config/*.php') as $file) {
            $key = basename($file, '.php'); 
            $this->mergeConfigFrom($file, $key);
        }
    }

    protected function registerRoutes()
    {
        \Route::middleware('web')
            ->namespace('BaseCms\Http\Controllers')
            ->group(__DIR__ . '/../../routes/web.php');
    }
}
