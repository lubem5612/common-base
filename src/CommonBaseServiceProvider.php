<?php

namespace Transave\CommonBase;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Transave\CommonBase\Console\Seeder;
use Transave\CommonBase\Http\Middlewares\AcceptNonNegativeAmount;
use Transave\CommonBase\Http\Middlewares\AccountVerification;
use Transave\CommonBase\Http\Middlewares\AllowIfAdmin;
use Transave\CommonBase\Http\Models\User;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;

class CommonBaseServiceProvider extends ServiceProvider
{

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/commonbase.php', 'commonbase');
        $this->mergeConfigFrom(__DIR__ . '/../config/endpoints.php', 'endpoints');
        $this->mergeConfigFrom(__DIR__ . '/../config/constants.php', 'constants');

        // Register the service the package provides.
        $this->app->singleton('commonbase', function ($app) {
            return new CommonBase;
        });
        //
        $this->app->bind('commonbase', function($app) {
            return new CommonBase;
        });
    }

    /**
     * Bootstrap the application services.
     * @param Kernel $kernel
     */
    public function boot(Kernel $kernel)
    {
        /*
         * Optional methods to load your package assets
         */
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'commonbase');
        $this->loadMigrationsFrom(__DIR__. '/../database/migrations');
        $this->registerRoutes();

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $this->defineDefaultConfig();

        //load global middleware
        $kernel->pushMiddleware(AcceptNonNegativeAmount::class);

        //load route middleware
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('admin', AllowIfAdmin::class);
        $router->aliasMiddleware('verification', AccountVerification::class);
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        $this->publishes([
            __DIR__ . '/../config/commonbase.php' => config_path('commonbase.php'),
        ], 'commbase-config');

        // Publishing migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'commonbase-migrations');

        // Publishing the views.
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/commonbase'),
        ], 'views');

        // Publishing assets.
        $this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/commonbase'),
        ], 'assets');

        // Registering package commands.
        $this->commands([
            Seeder::class,
        ]);
    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }

    protected function defineDefaultConfig()
    {
        Config::set('auth.defaults', [
            'guard'     => 'api',
            'passwords' => 'users',
        ]);

        Config::set('auth.guards.api', [
            'driver'    => 'session',
            'provider'  => 'users',
            'hash'      => false,
        ]);

        Config::set('auth.providers.users', [
            'driver'    => 'eloquent',
            'model'     => User::class,
        ]);

        Config::set('filesystems.disks.azure', [
            'driver'            => 'azure',
            'local_address'     => env('AZURE_STORAGE_LOCAL_ADDRESS', 'local'),
            'name'              => env('AZURE_STORAGE_NAME', 'raadaastorage'),
            'key'               => env('AZURE_STORAGE_KEY', ''),
            'container'         => env('AZURE_STORAGE_CONTAINER', "raadaatesting"),
            'prefix'            => env('AZURE_STORAGE_PREFIX', "transave"),
            'url'               => env('AZURE_STORAGE_URL', null),
        ]);
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
        return ['commonbase'];
    }

    protected function routeConfiguration()
    {
        return [
            'prefix'        => config('commonbase.route.prefix'),
            'middleware'    => config('commonbase.route.middleware'),
        ];
    }

//    protected function scheduler()
//    {
//        $this->app->booted(function () {
//            $schedule = $this->app->make(Schedule::class);
//            $schedule->command('transave:balance')->hourly();
//        });
//    }

}
