<?php


namespace Transave\CommonBase\Actions\Kuda\Account;



use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Transave\CommonBase\Actions\SMS\TermiiService;
use Transave\CommonBase\Helpers\KudaApiHelper;
use Transave\CommonBase\Helpers\PhoneHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;
use Transave\CommonBase\Http\Models\FailedAccount;
use Transave\CommonBase\Http\Models\Kyc;
use Transave\CommonBase\Http\Models\User;

class CreateVirtualAccount
{
    use ValidationHelper, ResponseHelper, PhoneHelper;

    private array $request;
    private array $validatedData;
    private array $kudaData;
    private $response_dump = [];
    private User $user;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            $this->validateRequest();
            $this->setTrackingReference();
            $this->setInternationalPhoneNumber();
            $this->setRole();
            $this->setPassword();
            $this->setVerificationDetails();
            $this->setAccountDefaults();
            $this->setKudaData();
            $this->setBusinessName();
            $this->setMiddleName();
            $this->fetchAccountIfExist();
            $this->createVirtualAccount();
            $this->createWalletAndKyc();
            return $this->sendNotification();
        }catch (\Exception $e) {
            $this->createOrUpdateFailedAccount();
            return $this->sendServerError($e);
        }
    }

    private function fetchAccountIfExist()
    {
        $failedAccount = FailedAccount::query()->where('email', $this->validatedData['email'])->first();
        if (!empty($failedAccount)) {
            $data = ['trackingReference' => $failedAccount->reference_id];

            $response = (new KudaApiHelper(['serviceType' => 'ADMIN_RETRIEVE_SINGLE_VIRTUAL_ACCOUNT', 'data' => $data]))->execute();
            if (User::query()->where('id', $failedAccount->reference_id)->doesntExist()) {
                if ($response['success']) {
                    DB::table('users')->insert([
                        'id' => $failedAccount->reference_id,
                        'first_name' => $response['data']['account']['firstName'],
                        'last_name' => $response['data']['account']['lastName'],
                        'email' => $response['data']['account']['email'],
                        'phone' => $response['data']['account']['phoneNumber'],
                        'account_number' => $response['data']['account']['accountNumber'],
                        'role' => 'customer',
                        'password' => bcrypt($this->validatedData['password']),
                        'verification_token' => rand(100000, 999999),
                        'account_verified_at' => Carbon::now()->addMinutes(15),
                        'is_verified' => "no",
                        'withdrawal_limit' => 0,
                        'account_type' => 'ordinary',
                        'account_status' => 'unverified',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                }
            }
        }
    }

    private function createVirtualAccount()
    {
        if (User::query()->where('email', $this->validatedData['email'])->exists()) {
            abort(403, 'email has been taken');
        }
        $response = (new KudaApiHelper(['serviceType' => 'ADMIN_CREATE_VIRTUAL_ACCOUNT', 'data' => $this->kudaData]))->execute();
        $this->response_dump = $response;
        if ($response['success'] && $response['data']['accountNumber']) {
            $this->validatedData['account_number'] = $response['data']['accountNumber'];

            DB::table('users')->insert($this->validatedData);
        }else {
            $this->createOrUpdateFailedAccount();
            abort(403, 'unable to create kuda account');
        }
        return $this;
    }

    private function setInternationalPhoneNumber()
    {
        $this->validatedData['phone'] = $this->getInternationalNumber($this->validatedData['phone']);
        return $this;
    }

    private function setVerificationDetails()
    {
        $this->validatedData['verification_token'] = rand(100000, 999999);
        $this->validatedData['account_verified_at'] = Carbon::now()->addMinutes(15);
        $this->validatedData['is_verified'] = "no";
        return $this;
    }

    private function setAccountDefaults()
    {
        $this->validatedData['withdrawal_limit'] = 0.00;
        $this->validatedData['account_type'] = "ordinary";
        $this->validatedData['account_status'] = "unverified";
        $this->validatedData['created_at'] = Carbon::now();
        $this->validatedData['updated_at'] = Carbon::now();

        return $this;
    }

    private function setTrackingReference()
    {
        $this->validatedData['id'] = Str::uuid()->toString();
        return $this;
    }

    private function setRole()
    {
        if (!array_key_exists('role', $this->validatedData)) {
            $this->validatedData['role'] = "customer";
        }
        return $this;
    }

    private function createWalletAndKyc()
    {
        Kyc::query()->create(['user_id' => $this->validatedData['id']]);
        return $this;
    }

    private function sendNotification()
    {
        $message = "Hello {$this->user->first_name}, use the code  {$this->user->verification_token} to activate your account. From Transave";
        (new TermiiService(['to' => $this->user->phone, 'sms' => $message]))->execute();

        return $this->sendSuccess($this->user, 'account created successfully');
    }

    private function setKudaData()
    {
        $this->kudaData = [
            "trackingReference" => $this->validatedData['id'],
            "email"             => $this->validatedData['email'],
            "phoneNumber"       => $this->localLocal($this->validatedData['phone']),
            "lastName"          => $this->validatedData['last_name'],
            "firstName"         => $this->validatedData['first_name']
        ];

        return $this;
    }

    private function setMiddleName()
    {
        if (array_key_exists('middle_name', $this->validatedData) && $this->validatedData['middle_name']) {
            $this->kudaData['middleName'] = $this->validatedData['middle_name'];
        }
        return $this;
    }

    private function setBusinessName()
    {
        if (array_key_exists('business_name', $this->validatedData) && $this->validatedData['business_name']) {
            $this->kudaData['businessName'] = $this->validatedData['business_name'];
        }
        return $this;
    }

    private function setPassword()
    {
        $this->validatedData['password'] = bcrypt($this->validatedData['password']);
        return $this;
    }

    private function createOrUpdateFailedAccount()
    {
        FailedAccount::query()->updateOrCreate(
            [
                'email' => $this->validatedData['email']
            ],
            [
                'reference_id' => $this->validatedData['id'],
                'email' => $this->validatedData['email'],
                'phone' => $this->validatedData['phone'],
                'data_dump' => json_encode($this->validatedData),
                'response' => json_encode($this->response_dump)
            ]
        );
    }

    private function validateRequest()
    {
        $data = $this->validate($this->request, [
            'first_name'            => 'required|string|max:50',
            'last_name'             => 'required|string|max:50',
            'middle_name'           => 'nullable|string|max:50',
            'business_name'         => 'nullable|string|max:150',
            "email"                 => "required|email",
            "phone"                 => "required|string|min:9|max:15",
            'password'              => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
            'role'                  => 'nullable|in:customer,staff'
        ]);

        $this->validatedData = Arr::except($data, ['password_confirmation']);

        return $this;
    }
}