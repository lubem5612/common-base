<?php


namespace Transave\CommonBase\Database\Seeders;


use Transave\CommonBase\Actions\Kuda\Account\CreateVirtualAccount;
use Transave\CommonBase\Actions\Kuda\Account\ListVirtualAccounts;

class KudaAccountSeeder
{
    public function run()
    {
        $this->fetchKudaAccount();
    }

    protected function fetchKudaAccount()
    {
        $total = 9;
        $index = 0;
        do {
            $response = (new ListVirtualAccounts(['PageNumber' => (string)($index + 1), 'PageSize' => '10']))->execute();
            if ($response['success'] && $response['data']['accounts'] && count($response['data']['accounts']) > 0) {
                $data = $response['data']['accounts'];
                $total = $response['data']['totalCount'];
                //create users;
                foreach ($data as $user) {
                    (new CreateVirtualAccount([
                        'first_name' => $user['firstName'],
                        'last_name' => $user['lastName'],
                        'middle_name' => $user['middleName'],
                        'business_name' => $user['bussinessName'],
                        'email' => $user['email'],
                        'phone' => $user['phoneNumber'],
                        'account_number' => $user['accountNumber'],
                        'withdrawal_limit' => 0.00,
                        'role' => 'customer',
                        'password' => 'transave'
                    ]))->execute();
                }
            }
            $index++;
        } while (($index * 10) <= $total);
    }

}