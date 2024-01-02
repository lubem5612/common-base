<?php


namespace Transave\CommonBase\Actions\Flutterwave;


use Flutterwave\Flutterwave;
use Flutterwave\Util\Currency;
use Illuminate\Support\Arr;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\SessionHelper;
use Transave\CommonBase\Helpers\ValidationHelper;
use Transave\CommonBase\Http\Models\User;

class InitiateBankTransfer
{
    use ValidationHelper, ResponseHelper, SessionHelper;
    private $request, $validatedData;
    private $bankPayment, $flutterwaveData = [];
    private $user;

    public function __construct(array $request)
    {
        $this->request = $request;
        $this->bankPayment = Flutterwave::create('account');
    }

    public function execute()
    {
        try {
            $this->validateRequest();
            $this->setUser();
            $this->setTransactionData();
            $this->setCustomerData();
            return $this->initiateCharge();
        }catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    private function setUser()
    {
        if (Arr::exists($this->validatedData, 'user_id') && $this->validatedData['user_id']) {
            $this->user = User::query()->find($this->validatedData['user_id']);
        }else {
            $this->user = auth()->user();
        }
    }

    private function setCustomerData()
    {
        $this->flutterwaveData['customer'] = $this->bankPayment->customer->create([
            "full_name" => $this->user->first_name.' '.$this->user->last_name,
            "email" => $this->user->email,
            "phone" => $this->user->phone,
        ]);
    }

    private function initiateCharge()
    {
        $payload = $this->bankPayment->payload->create($this->flutterwaveData);
        $response = $this->bankPayment->initiate($payload);
        return $this->sendSuccess($response, 'bank transaction initiated');
    }

    private function setTransactionData()
    {
        $this->flutterwaveData = [
            "amount" => $this->validatedData['amount'],
            "currency" => Currency::NGN,
            "tx_ref" => $this->generateReference(),
            "additionalData" => [
                "account_details" => [
                    "account_bank" => $this->validatedData['account_bank'],
                    "account_number" => $this->validatedData['account_number'],
                    "country" => "NG"
                ]
            ],
        ];
    }

    public function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "amount" =>"required|numeric|gt:0",
            "account_bank" => "required|size:3",
            "account_number" => "required",
            "user_id" => "sometimes|required|exists:users,id"
        ]);
    }

}