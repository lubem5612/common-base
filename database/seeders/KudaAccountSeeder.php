<?php


namespace Transave\CommonBase\Database\Seeders;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Transave\CommonBase\Actions\Kuda\Account\ListVirtualAccounts;
use Transave\CommonBase\Actions\SMS\TermiiService;
use Transave\CommonBase\Http\Models\Kyc;
use Transave\CommonBase\Http\Models\User;

class KudaAccountSeeder
{
    public function run()
    {
        if (User::query()->where('role', 'customer')->doesntExist()) {
            $this->fetchKudaAccount();
        }
    }

    protected function fetchKudaAccount()
    {
        $total = 9;
        $index = 0;
        do {
            $response = (new ListVirtualAccounts(['PageNumber' => (string)($index + 1), 'PageSize' => '10']))->execute();
            if ($response['success'] && $response['data']['accounts']) {
               // dd($response);
                $data = $response['data']['accounts'];
                $total = $response['data']['totalCount'];
                //create users;
                foreach ($data as $user) {
                    if (User::query()->where('id', $user['trackingReference'])->doesntExist()) {
                        DB::table('users')->insert([
                            'id' => $user['trackingReference'],
                            'first_name' => $user['firstName'],
                            'last_name' => $user['lastName'],
                            'middle_name' => $user['middleName'],
                            'business_name' => $user['bussinessName'],
                            'email' => $user['email'],
                            'phone' => $user['phoneNumber'],
                            'account_number' => $user['accountNumber'],
                            'withdrawal_limit' => 0.00,
                            'role' => 'customer',
                            'password' => bcrypt('transave'),
                            'verification_token' => rand(100000, 999999),
                            'account_verified_at' => Carbon::now()->addMinutes(15),
                            'is_verified' => 'no',
                            'account_type' => 'ordinary',
                            'account_status' => 'unverified',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        $item = User::query()->find($user['trackingReference']);

                        $this->createWalletAndKyc($item);
                        $this->sendNotification($item);
                    }
                }
            }
            $index++;
        } while (($index * 10) <= $total);
    }

    private function createWalletAndKyc($user)
    {
        Kyc::query()->create(['user_id' => $user->id]);
    }

    private function sendNotification($user)
    {
        $message = "Hello {$user->first_name}, use the code  {$user->verification_token} to activate your account. From Transave";
        (new TermiiService(['to' => $user->phone, 'sms' => $message]))->execute();
    }

}