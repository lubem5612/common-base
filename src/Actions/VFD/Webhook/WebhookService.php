<?php


namespace Transave\CommonBase\Actions\VFD\Webhook;


use App\Jobs\WalletCreditJob;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Helpers\Constants;
use Transave\CommonBase\Http\Models\AccountNumber;
use Transave\CommonBase\Http\Models\Transaction;

class WebhookService extends Action
{
    private $request, $wallet;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        try {
            $this->getUser();
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
        return response()->json(['message' => $message, 'data' => $this->request, 'success' => true ], 200);
    }
}
