<?php


namespace Transave\CommonBase\Actions\VFD\Webhook;


use App\Jobs\WalletCreditJob;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Helpers\Constants;
use Transave\CommonBase\Helpers\UtilsHelper;
use Transave\CommonBase\Http\Models\AccountNumber;
use Transave\CommonBase\Http\Models\Transaction;

class WebhookService extends Action
{
    use UtilsHelper;

    private $request, $wallet;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        try {
            $this->getUser();
            $this->getBankName();
            $this->getSenderAndRecipientName();
            return $this->sendResponse();
        } catch (HttpException $e) {
            return $this->sendServerError($e, $e->getStatusCode());
        }
    }

    private function getUser()
    {
        $account = AccountNumber::where('account_number', $this->request['account_number'])->with(['wallet'])->first();
        abort_unless(!is_null($account), 200, 'Webhook call successful');
        
        $this->wallet = $account->wallet;
    }

    private function getBankName()
    {
        $this->request['senderBank'] = $this->getBankNameByCode($this->request['originator_bank']);
        $this->request['recipientBank'] = config('commonbase.vfd.bank_name');
    }

    private function getSenderAndRecipientName()
    {
        $recipientAccount = AccountNumber::whereAccountNumber($this->request['account_number'])->with('user')->first();
        $this->request['toClient'] = $this->removeStrings($recipientAccount->user->fullName, [Constants::WALLET_PREFIX]);

        $this->request['fromClient'] = $this->request['originator_account_name'];
    }

    private function sendResponse()
    {
        $message = 'Webhook received successfully';
        $transaction = Transaction::where('reference', $this->request['reference'])->first();
        if ($transaction) {
            $message = 'Already processed';
        } else {
            $data = [
                'user_id' => $this->wallet->user_id,
                'amount' => $this->request['amount'],
                'reference' => $this->request['reference'],
                'commission' => 0.00,
                'charge' => 0.00,
                'type' => Constants::TRANSACTION_TYPE['CREDIT'],
                'description' => $this->request['originator_narration'],
                'category' => Constants::CATEGORIES['WALLET_FUNDING'],
                'status' => Constants::SUCCESSFUL,
                'payload' => json_encode($this->request)
            ];
            WalletCreditJob::dispatch($data);
        }
        $dto = Arr::except($this->request, ['toClient', 'fromClient', 'senderBank', 'recipientBank']);
        return response()->json(['message' => $message, 'data' => $dto, 'success' => true ], 200);
    }
}
