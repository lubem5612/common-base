<?php

namespace Raadaapartners\Raadaabase;

use Illuminate\Support\ServiceProvider;

class RaadaabaseServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'raadaapartners');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'raadaapartners');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/raadaabase.php', 'raadaabase');
        $this->mergeConfigFrom(__DIR__.'/../config/constants.php', 'constants');

        // Register the service the package provides.
        $this->app->singleton('raadaabase', function ($app) {
            return new Raadaabase;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['raadaabase'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/raadaabase.php' => config_path('raadaabase.php'),
            __DIR__.'/../config/constants.php' => config_path('constants.php'),
        ], 'raadaabase.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/raadaapartners'),
        ], 'raadaabase.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/raadaapartners'),
        ], 'raadaabase.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/raadaapartners'),
        ], 'raadaabase.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
