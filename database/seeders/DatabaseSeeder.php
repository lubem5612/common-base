<?php


namespace Transave\CommonBase\Database\Seeders;


class DatabaseSeeder
{

    public function definition()
    {
        return [
            'countries' => CountryTableSeeder::class,
            'states' => StateTableSeeder::class,
            'users' => UserTableSeeder::class,
            'virtual_accounts' => KudaAccountSeeder::class,
        ];
    }

}