<?php


namespace Transave\CommonBase\Actions\VFD\Transfer;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Transave\CommonBase\Actions\Transaction\UpdateTransaction;
use Transave\CommonBase\Actions\User\VerifyTransactionPin;
use Transave\CommonBase\Helpers\BalanceHelper;
use Transave\CommonBase\Helpers\Constants;
use Transave\CommonBase\Helpers\UtilsHelper;
use Transave\CommonBase\Helpers\VfdApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\SessionHelper;
use Transave\CommonBase\Helpers\ValidationHelper;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Transave\CommonBase\Actions\Transaction\CreateTransaction;


class BankTransfer
{
    use ResponseHelper, ValidationHelper, SessionHelper, BalanceHelper, UtilsHelper;
    private array $request, $transaction, $validatedData;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            $this->validateRequest();
            $this->validateTransactionPin();
            $this->generateRequestReference();
            $this->getBankName();
            $this->validateAndDebitUser();
            return $this->processTransfer();
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
        
        $response = (new VfdApiHelper($data, '/transfer', 'post'))->execute();
        if (isset($response['status']) && $response['status'] == '00') {
            (new UpdateTransaction([
                'transaction_id'    => $this->transaction['id'],
                'status'            => Constants::SUCCESSFUL,
                'payload'           => json_encode([...$this->validatedData, ...$response])
            ]))->execute();
        }

        return $response;
    }

    private function validateTransactionPin()
    {
        $response = (new VerifyTransactionPin([
            'user_id' => auth()->id(),
            'transaction_pin' => $this->validatedData['transaction_pin']
        ]))->execute();

        $data = json_decode($response->getContent(), true);
        unset($this->validatedData['transaction_pin']);
        abort_unless($data['success'], 403, 'Incorrect transaction PIN, please try again');
    }

    private function generateRequestReference()
    {
        $this->validatedData['reference'] = $this->generateReference();
    }

    private function getBankName()
    {
        $this->validatedData['recipientBank'] = $this->getBankNameByCode($this->validatedData['toBank']);
        $this->validatedData['senderBank'] = config('commonbase.vfd.bank_name');
    }

    private function validateAndDebitUser()
    {
        DB::transaction(function () {
            $user_id = $this->validatedData['user_id'];
            $amount = $this->validatedData['amount'];
            $transferFee = config('commonbase.vfd.transfer_fee');
            $transferCommission = config('commonbase.vfd.app_transfer_fee');
            $commission = $transferCommission > $transferFee ? ($transferCommission - $transferFee) : 0.00;

            $totalAmount = $amount + $transferCommission;
            if (!$this->debitWallet($user_id, $totalAmount)) {
                abort(403, 'Insufficient wallet balance');
            }
    
            $response = (new CreateTransaction([
                'user_id' => auth()->id(),
                'reference' => $this->validatedData['reference'],
                'amount' => $this->validatedData['amount'],
                'charges' => $transferCommission,
                'commission' => $commission,
                'type' => Constants::TRANSACTION_TYPE['DEBIT'],
                'description' => $this->validatedData['remark'],
                'category' => Constants::CATEGORIES['BANK_TRANSFER'],
                'status' => Constants::PROCESSING,
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
                    'status' => Constants::SUCCESSFUL,
                    'payload' => json_encode($this->validatedData),
                ]))->execute();
            }

            $data = json_decode($response->getContent(), true);
            $this->transaction = $data['data'];
        });
    }

    private function validateRequest()
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
    }
}
