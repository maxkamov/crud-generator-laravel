<?php

namespace Maxkamov48\CrudGeneratorLaravel;

use Illuminate\Support\ServiceProvider;
use Maxkamov48\CrudGeneratorLaravel\Commands\TestCommand;

class CrudGeneratorProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TestCommand::class,
            ]);
        }

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
