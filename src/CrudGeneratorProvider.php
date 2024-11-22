<?php

namespace Maxkamov48\CrudGeneratorLaravel;

use Illuminate\Support\ServiceProvider;

class CrudGeneratorProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/crud-generator.php' => config_path('crud-generator.php'),
        ]);
    }

    public function register()
    {
        $this->app->singleton('CrudGenerator', function ($app) {
            return new CrudGenerator();
        });
    }
}
