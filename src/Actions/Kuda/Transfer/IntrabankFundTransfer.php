<?php


namespace Transave\CommonBase\Actions\Kuda\Transfer;


use Illuminate\Support\Arr;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Helpers\WithdrawalLimitHelper;
use Transave\CommonBase\Http\Models\User;

class IntrabankFundTransfer extends Action
{
    private array $request;
    private array $validatedData;
    private $withdrawal, $nameEnquiry;
    private ?User $beneficiary;
    private $sender;

    public function __construct(array $request)
    {
        $this->request = $request;
        if (Arr::exists($request, 'sender_id') && $this->request['sender_id']) {
            $this->sender = User::query()->find($this->request['sender_id']);
        }else {
            $this->sender = auth()->user();
        }
        $this->withdrawal = new WithdrawalLimitHelper($this->sender->id);
    }

    public function handle()
    {
        return $this
            ->validateRequest()
            ->getBeneficiaryAccount()
            ->checkWalletBalance()
            ->setNarration()
            ->transferFund();
    }

    private function transferFund()
    {
        $response = (new VirtualAccountFundTransfer([
            "user_id" => $this->sender->id,
            "beneficiary_account_number" => $this->beneficiary->account_number,
            "beneficiary_bank_code" => config('commonbase.kuda.bank_code'),
            "beneficiary_name" => $this->nameEnquiry['data']['beneficiaryName'],
            "amount" => $this->validatedData['amount'],
            "narration" => $this->validatedData['narration'],
            "name_enquiry_sessionID" => $this->nameEnquiry['data']['sessionID'],
            "client_fee_charge" => 0
        ]))->execute();

        return $response;
    }

    private function getBeneficiaryAccount()
    {
        $this->beneficiary = User::query()->find($this->validatedData['beneficiary_user_id']);

        $this->nameEnquiry = (new NameEnquiry([
            "beneficiary_account_number" => $this->beneficiary->account_number,
            "beneficiary_bank_code" => config('commonbase.kuda.bank_code'),
            "user_id" => $this->sender->id,
        ]))->execute();

        abort_unless($this->nameEnquiry['success'], 403, 'cant retrieve beneficiary account');
        return $this;
    }

    private function checkWalletBalance()
    {
        $balance = $this->withdrawal->currentWalletBalance();
        abort_if(($balance < $this->validatedData['amount']), 403, "insufficient balance");
        return $this;
    }

    private function setNarration()
    {
        if (!array_key_exists('narration', $this->validatedData)) {
            $this->validatedData['narration'] = "Wallet transfer from ".$this->sender->first_name." to {$this->beneficiary->first_name}";
        }
        return $this;
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'beneficiary_user_id' => 'required|exists:users,id',
            'sender_user_id' => 'nullable|exists:users,id',
            'amount' => "required|numeric|gt:0|lte:{$this->withdrawal->currentLimit()}",
//            'amount' => "required|numeric|gt:0",
            'narration' => "nullable|string"
        ]);
        return $this;
    }
}