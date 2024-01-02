<?php


namespace Transave\CommonBase\Actions\Flutterwave;


use Flutterwave\Flutterwave;
use Flutterwave\Util\Currency;
use Illuminate\Support\Arr;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\SessionHelper;
use Transave\CommonBase\Helpers\ValidationHelper;
use Transave\CommonBase\Http\Models\User;

class InitiateCardTransaction
{
    use ResponseHelper, ValidationHelper, SessionHelper;
    private $cardPayment, $flutterwaveData = [];
    private $request, $validatedData;
    private $user;

    public function __construct(array $request)
    {
        $this->request = $request;
        $this->cardPayment = Flutterwave::create("card");
    }

    public function execute()
    {
        try {
            $this->validatedRequest();
            $this->setUser();
            $this->setTransactionData();
            $this->setCustomerData();
            return $this->createCardTransaction();
        }catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    private function setTransactionData()
    {
        $this->flutterwaveData = [
            "amount" => $this->validatedData['amount'],
            "currency" => Currency::NGN,
            "tx_ref" => $this->generateReference(),
            "redirectUrl" => route('flutterwave.redirect', ['id' => $this->user->id]),
            "additionalData" => [
                "meta" => [
                    "unique_id" => uniqid().uniqid()
                ],
                "preauthorize" => false,
                "payment_plan" => null,
                "card_details" => [
                    "card_number" => $this->validatedData['card_number'],
                    "cvv" => $this->validatedData['cvv'],
                    "expiry_month" => $this->validatedData['expiry_month'],
                    "expiry_year" => $this->validatedData['expiry_year'],
                ]
            ],
        ];
    }

    private function setCustomerData()
    {
        $this->flutterwaveData['customer'] = $this->cardPayment->customer->create([
            "full_name" => $this->user->first_name.' '.$this->user->last_name,
            "email" => $this->user->email,
            "phone" => $this->user->phone,
        ]);
    }

    private function createCardTransaction()
    {
        $payload = $this->cardPayment->payload->create($this->flutterwaveData);
        $response = $this->cardPayment->initiate($payload);
        return $this->sendSuccess($response, 'card transaction initiated');
    }

    private function setUser()
    {
        if (Arr::exists($this->validatedData, 'user_id') && $this->validatedData['user_id']) {
            $this->user = User::query()->find($this->validatedData['user_id']);
        }else {
            $this->user = auth()->user();
        }
    }

    private function validatedRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "card_number" => "required|numeric",
            "cvv" => "required|string|size:3",
            "expiry_month" => "required|string|size:2",
            "expiry_year" => "required|string|size:2",
            "amount" => "required|numeric|gt:0",
            "user_id" => "nullable"
        ]);
        return $this;
    }
}