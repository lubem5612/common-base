<?php


namespace Transave\CommonBase\Actions\Kuda\Transfer;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Helpers\KudaApiHelper;

class MainAccountFundTransfer extends Action
{
    private array $request;
    private array $validatedData;
    private array $kudaData;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        return $this
            ->validateRequest()
            ->setKudaData()
            ->setChargeClient()
            ->outwardTransfer();
    }

    private function outwardTransfer()
    {
        $this->kudaData["clientAccountNumber"] = config('transave.kuda.acc_number');
        $this->kudaData["senderName"] = config('transave.kuda.acc_name');

        return (new KudaApiHelper(['serviceType' => 'SINGLE_FUND_TRANSFER', 'data' => $this->kudaData]))->execute();
    }

    private function setKudaData()
    {
        $this->kudaData = [
            "beneficiaryBankCode" => $this->validatedData["beneficiary_bank_code"],
            "beneficiaryAccount" => $this->validatedData["beneficiary_account_number"],
            "beneficiaryName" => $this->validatedData["beneficiary_name"],
            "amount" => $this->validatedData["amount"] * 100,
            "narration" => $this->validatedData["narration"],
            "nameEnquirySessionID" => $this->validatedData["name_enquiry_sessionID"],
        ];
        return $this;
    }

    private function setChargeClient()
    {
        if (array_key_exists('client_fee_charge', $this->validatedData)) {
            $this->kudaData["clientFeeCharge"] = $this->validatedData["client_fee_charge"];
        }
        return $this;
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "beneficiary_bank_code" => "required",
            "beneficiary_account_number" => "required",
            "beneficiary_name" => "required|string|max:100",
            "amount" => "required|numeric|gt:0",
            "narration" => "required|string|max:150",
            "name_enquiry_sessionID" => "required",
            "client_fee_charge" => "nullable|numeric|gte:0"
        ]);

        return $this;
    }
}