<?php

namespace BaseCms\Providers;

use Illuminate\Support\ServiceProvider;

class CmsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerRoutes();

        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'cms');

        $this->publishes([
            __DIR__.'/../../config/cms.php' => config_path('cms.php'),
        ], 'cms-config');

        $this->publishes([
            __DIR__.'/../../resources/js' => resource_path('vendor/cms-core/js'),
        ], 'cms-assets');
    }


    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/cms.php', 'cms');
    }

    protected function registerRoutes()
    {
        \Route::middleware('web')
            ->namespace('BaseCms\Http\Controllers')
            ->group(__DIR__.'/../../routes/web.php');
    }
}
