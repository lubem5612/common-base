<?php


namespace Transave\CommonBase\Database\Seeders;



use Transave\CommonBase\Http\Models\Country;

class CountryTableSeeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = config('constants.countries');
        foreach ($countries as $index => $country) {
            Country::query()->updateOrCreate([ 'name' => $country['country']], ['code' => $index, 'name' => $country['country'], 'continent' => $country['continent'] ]);
        }
    }
}