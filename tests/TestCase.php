<?php


namespace Raadaapartners\Raadaabase\Tests;
use Raadaapartners\Raadaabase\RaadaabaseServiceProvider;


class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }

    protected function getPackageProviders($app)
    {
        return [
            RaadaabaseServiceProvider::class;
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
