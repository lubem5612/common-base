<?php


namespace Transave\CommonBase\Actions\VFD\Transfer;


use Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Transave\CommonBase\Helpers\BalanceHelper;
use Transave\CommonBase\Helpers\Constants;
use Transave\CommonBase\Helpers\VfdApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\SessionHelper;
use Transave\CommonBase\Helpers\ValidationHelper;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Transave\CommonBase\Actions\Transaction\CreateTransaction;


class BankTransfer
{
    use ResponseHelper, ValidationHelper, SessionHelper, BalanceHelper;
    private array $request;
    private array $validatedData;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            return $this->validateRequest()
            ->validateTransactionPin()
            ->generateRequestReference()
            ->validateAndDebitUser()
            ->processTransfer();
        } catch (HttpException $e) {
            return $this->sendServerError($e, $e->getStatusCode());
        }
    }

    private function processTransfer()
    {
        $data = Arr::except($this->validatedData, ['user_id', 'transfer_fee']);
        $data['fromAccount'] = config('commonbase.vfd.pool_acc_number');
        $data['fromClientId'] = config('commonbase.vfd.pool_client_id');
        $data['fromSavingsId'] = config('commonbase.vfd.pool_savings_id');
        
        return (new VfdApiHelper($data, '/transfer', 'post'))->execute();
    }

    private function validateTransactionPin()
    {
        if (!Hash::check($this->validatedData['transaction_pin'], auth()->user()->transaction_pin)) {
            abort(403, 'Incorrect transaction PIN, please try again');
        }
        
        return $this;
    }

    private function generateRequestReference()
    {
        $this->validatedData['reference'] = $this->generateReference();
        return $this;
    }

    private function validateAndDebitUser()
    {
        DB::transaction(function () {
            $user_id = $this->validatedData['user_id'];
            $amount = $this->validatedData['amount'];
            if (!$this->debitWallet($user_id, $amount)) {
                abort(403, 'Insufficient wallet balance');
            }
    
            $transferFee = config('commonbase.vfd.transfer_fee');
            $transferCommission = config('commonbase.vfd.app_transfer_fee');
            $commission = $transferCommission > $transferFee ? ($transferCommission - $transferFee) : 0.00;
    
            (new CreateTransaction([
                'user_id' => auth()->id(),
                'reference' => $this->validatedData['reference'],
                'amount' => $this->validatedData['amount'],
                'charges' => $transferCommission,
                'commission' => $commission,
                'type' => 'debit',
                'description' => $this->validatedData['remark'],
                'category' => Constants::CATEGORIES['BANK_TRANSFER'],
                'status' => 'processing',
                'payload' => json_encode($this->validatedData),
            ]))->execute();
            
            if ($commission > 0) {
                (new CreateTransaction([
                    'user_id' => auth()->id(),
                    'reference' => $this->validatedData['reference'],
                    'amount' => $transferCommission,
                    'charges' => 0.00,
                    'commission' => 0.00,
                    'type' => 'debit',
                    'description' => $this->validatedData['remark'],
                    'category' => Constants::CATEGORIES['BANK_TRANSFER_COMMISSION'],
                    'status' => 'processing',
                    'payload' => json_encode($this->validatedData),
                ]))->execute();
            }
        });

        return $this;
    }

    private function validateRequest() : self
    {
        $this->request['user_id'] = auth()->user()->id;
        $this->request['signature'] = hash(
            'sha512',
            config('commonbase.vfd.pool_acc_number'
        ).$this->request['toAccount']);
        $requiredString = 'required|string';
        $optionalString = 'nullable|string';
        $requiredStringIfIntra = 'string|required_if:transferType,=,intra';
        $requiredStringIfInter = 'string|required_if:transferType,=,inter';
        $this->validatedData = $this->validate($this->request, [
            'user_id'               => $requiredString,
            'fromAccount'           => $requiredString,
            'fromClientId'          => $requiredString,
            'fromClient'            => $requiredString,
            'fromSavingsId'         => $requiredString,
            'uniqueSenderAccountId' => $requiredString,
            'fromBvn'               => $optionalString,
            'toClientId'            => $requiredStringIfIntra,
            'toClient'              => $requiredString,
            'toSavingsId'           => $requiredStringIfIntra,
            'toSession'             => $requiredStringIfInter,
            'toBvn'                 => $optionalString,
            'toAccount'             => $requiredString,
            'toBank'                => $requiredString,
            'signature'             => $requiredString,
            'amount'                => 'required|numeric|gt:0',
            'transferType'          => 'required|string|in:inter,intra',
            'remark'                => 'required|string|max:150',
            'transfer_fee'          => 'nullable|numeric|gte:0',
            'transaction_pin'       => 'required|digits:4'
        ]);

        return $this;
    }
}
