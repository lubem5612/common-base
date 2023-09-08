<?php


namespace Transave\CommonBase\Console;


use Illuminate\Console\Command;
use Transave\CommonBase\Database\Seeders\DatabaseSeeder;

class Seeder extends Command
{
    protected $signature = 'transave:seed';
    protected $description = 'seed package data to tables';

    public function handle()
    {
        $seeders = (new DatabaseSeeder())->definition();
        foreach ($seeders as $index => $seeder) {
            $this->info('seeding '.$index.' begins');
            $seed = new $seeder();
            $seed->run();
            $this->info($index.'seeded successfully');
        }
    }
}