<?php


namespace Transave\CommonBase\Database\Seeders;


use Transave\CommonBase\Http\Models\Country;
use Transave\CommonBase\Http\Models\Lga;
use Transave\CommonBase\Http\Models\State;

class StateTableSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $states = config("constants.states");
        $country = Country::query()->where('name', 'Nigeria')->first();
        foreach ($states as $index => $state) {
            $st = State::query()->updateOrCreate([
                'country_id' => $country->id,
                'name' => $state['name']
            ], [
                'country_id' => $country->id,
                'name' => $state['name'],
                'capital' => $state['capital']
            ]);
            foreach ($state['lga'] as $lg) {
                Lga::query()->updateOrCreate([
                    'name' => $lg,
                ], [
                    'state_id' => $st->id,
                    'name' => $lg,
                ]);
            }
        }
    }
}