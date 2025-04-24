<?php


namespace Transave\CommonBase\Actions\VFD\Account;



use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Transave\CommonBase\Actions\SMS\TermiiService;
use Transave\CommonBase\Helpers\VfdApiHelper;
use Transave\CommonBase\Helpers\PhoneHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;
use Transave\CommonBase\Http\Models\FailedAccount;
use Transave\CommonBase\Http\Models\Kyc;
use Transave\CommonBase\Http\Models\User;
use Transave\CommonBase\Http\Models\AccountNumber;
use Transave\CommonBase\Http\Models\Wallet;

class CreateVirtualAccount
{
    use ValidationHelper, ResponseHelper, PhoneHelper;

    private $request;
    private $validatedData;
    private $vfdData;
    private $responseDump = [];

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
            $this->setBusinessName();
            $this->setMiddleName();
            $this->fetchAccountIfExist();
            $this->createVirtualAccount();
            $this->createKyc();
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
            $endpoint = '/account/enquiry?accountNumber=' . $failedAccount->reference_id;

            $response = (new VfdApiHelper([], $endpoint, 'post'))->execute();
            if (isset($response["data"]["accountNo"]) && User::query()->where('id', $failedAccount->reference_id)->doesntExist()) {
                DB::transaction(function () use($response) {
                    DB::table('users')->insert([
                        'id' => $this->validatedData['id'],
                        'first_name' => $this->validatedData["first_name"],
                        'last_name' => $this->validatedData["last_name"],
                        'middle_name' => $this->validatedData['middle_name'],
                        'email' => $this->validatedData['email'],
                        'phone' => $this->validatedData['phone'],
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

                    AccountNumber::create([
                        'user_id' => $this->validatedData['id'],
                        'account_number' => $response['data']['accountNo'],
                        'bank_name' => config('commonbase.vfd.bank_name'),
                    ]);
        
                    Wallet::create([
                        'user_id' => $this->validatedData['id'],
                    ]);
                });
            }
        }
    }

    private function createVirtualAccountWithNIN()
    {
        $nin = $this->validatedData["nin"];
        $date = $this->validatedData["dob"];
        $dob = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
        $endpoint = "/client/tiers/individual?nin=$nin&dateOfBirth=$dob";
        return (new VfdApiHelper([], $endpoint, 'post'))->execute();
    }

    private function createVirtualAccountWithBVN()
    {
        $nin = $this->validatedData["bvn"];
        $date = $this->validatedData["dob"];
        $dob = date('d-M-Y', strtotime(str_replace('/', '-', $date)));
        $endpoint = "/client/tiers/individual?bvn=$nin&dateOfBirth=$dob";
        return (new VfdApiHelper([], $endpoint, 'post'))->execute();
    }

    private function createVirtualAccount()
    {
        if (User::query()->where('email', $this->validatedData['email'])->exists()) {
            abort(403, 'email has been taken');
        }
        $response = null;
        if (isset($this->validatedData["nin"])) {
            $response = $this->createVirtualAccountWithNIN();
        } elseif (isset($this->validatedData["bvn"])) {
            $response = $this->createVirtualAccountWithBVN();
        }
        
        $this->responseDump = $response;
        
        if (isset($response['data']) && $response['data']['accountNo']) {
            $this->validatedData["withdrawal_limit"] = config('commonbase.vfd.withdrawal_limit');

            DB::transaction(function () use($response) {
                $userData =  Arr::except($this->validatedData, ['password_confirmation', 'nin', 'bvn']);
                DB::table('users')->insert($userData);
    
                AccountNumber::create([
                    'user_id' => $this->validatedData['id'],
                    'account_number' => $response['data']['accountNo'],
                    'bank_name' => config('commonbase.vfd.bank_name'),
                ]);
    
                Wallet::create([
                    'user_id' => $this->validatedData['id'],
                ]);
            });
        } else {
            $this->createOrUpdateFailedAccount();
            abort(403,$response['message']);
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

    private function createKyc()
    {
        Kyc::query()->create([
            'user_id' => $this->validatedData['id'],
            'identity_type' => ($this->validatedData['nin']) ? 'NIN' : 'BVN',
            'verification_payload' => json_encode($this->responseDump)
        ]);
        return $this;
    }

    private function sendNotification()
    {
        $firstName = $this->validatedData['first_name'];
        $token = $this->validatedData['verification_token'];
        $phone = $this->validatedData['phone'];

        $message = "Hello {$firstName}, use the code  {$token} to activate your account. From Transave";
        (new TermiiService(['to' => $phone, 'sms' => $message]))->execute();

        return $this->sendSuccess(User::query()->find($this->validatedData['id']), 'account created successfully');
    }

    private function setMiddleName()
    {
        if (array_key_exists('middle_name', $this->validatedData) && $this->validatedData['middle_name']) {
            $this->vfdData['middleName'] = $this->validatedData['middle_name'];
        }
        return $this;
    }

    private function setBusinessName()
    {
        if (array_key_exists('business_name', $this->validatedData) && $this->validatedData['business_name']) {
            $this->vfdData['businessName'] = $this->validatedData['business_name'];
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
                'response' => json_encode($this->responseDump)
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
            'role'                  => 'nullable|in:customer,staff',
            'dob'                   => 'required|date',
            'nin'                   => 'sometimes|required|string|max:20',
            'bvn'                   => 'sometimes|required|string|max:20'
        ]);

        $this->validatedData = Arr::except($data, ['password_confirmation']);

        return $this;
    }
}
