<?php


namespace Transave\CommonBase\Database\Seeders;


use Carbon\Carbon;
use Transave\CommonBase\Http\Models\User;

class UserTableSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (User::query()->where('role', 'admin')->doesntExist()) {
            $name = explode(' ', config('commonbase.kuda.acc_name'));
            User::query()->create([
                'first_name' => $name[0],
                'last_name' => $name[1],
                'business_name' => 'Transave Limited',
                'email' => config('commonbase.kuda.email'),
                'phone' => config('commonbase.kuda.phone_no'),
                'account_number' => config('commonbase.kuda.acc_number'),
                'withdrawal_limit' => 5000000,
                'role' => 'admin',
                'account_verified_at' => Carbon::now(),
                'is_verified' => 'yes',
                'account_type' => 'super',
                'account_status' => 'verified',
                'password' => bcrypt('password')
            ]);
        }
    }
}