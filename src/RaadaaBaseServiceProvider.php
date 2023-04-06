<?php

namespace Raadaapartners\Raadaabase;

use Illuminate\Support\ServiceProvider;

class RaadaabaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'raadaabase');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'raadaabase');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/raadaabase.php' => config_path('raadaabase.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/raadaabase'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/raadaabase'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/raadaabase'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/raadaabase.php', 'raadaabase');
        $this->mergeConfigFrom(__DIR__ . '/../config/constants.php', 'constants');

        // Register the main class to use with the facade
        $this->app->singleton('raadaabase', function () {
            return new Raadaabase;
        });
    }
}
