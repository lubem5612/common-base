<?php


namespace Transave\CommonBase\Tests;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\SanctumServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Transave\CommonBase\CommonBaseServiceProvider;
use Transave\CommonBase\Database\Seeders\KudaAccountSeeder;

class TestCase extends BaseTestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        (new KudaAccountSeeder())->run();
    }

    protected function getPackageProviders($app)
    {
        return [
            CommonBaseServiceProvider::class,
            SanctumServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'../database/migrations');
    }
}