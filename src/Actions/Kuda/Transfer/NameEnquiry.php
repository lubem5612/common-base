<?php


namespace Transave\CommonBase\Actions\Kuda\Transfer;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Helpers\KudaApiHelper;

class NameEnquiry extends Action
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
            ->checkIfIsVirtualAccount()
            ->fetchBeneficiaryAccount();
    }

    private function fetchBeneficiaryAccount()
    {
        return (new KudaApiHelper(['serviceType' => 'NAME_ENQUIRY', 'data' => $this->kudaData]))->execute();
    }

    private function setKudaData()
    {
        $this->kudaData = [
            "beneficiaryAccountNumber" => $this->validatedData['beneficiary_account_number'],
            "beneficiaryBankCode" => $this->validatedData['beneficiary_bank_code'],
        ];

        return $this;
    }

    private function checkIfIsVirtualAccount()
    {
        if (array_key_exists('user_id', $this->validatedData) && $this->validatedData['user_id']) {
            $this->kudaData['isRequestFromVirtualAccount'] = "true";
            $this->kudaData['senderTrackingReference'] = $this->validatedData['user_id'];
        }
        return $this;
    }

    private function validateRequest() : self
    {
        $this->validatedData = $this->validate($this->request, [
            "beneficiary_account_number" => "required|string|min:10|max:10",
            "beneficiary_bank_code" => "required|string|min:3",
            "user_id" => "nullable",
        ]);

        return $this;
    }
}