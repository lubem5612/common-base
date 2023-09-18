<?php


namespace Transave\CommonBase\Actions\Kuda\Webhook;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Actions\Transaction\CreateTransaction;
use Transave\CommonBase\Http\Models\User;

class WebhookService extends Action
{
    private $request, $user, $response;

    /**
     * WebhookService constructor.
     * @param $request
     *
     * sample data post from kuda
     * {
     * "payingBank": "Access Bank Plc",
     * "amount": "57000",
     * "transactionReference": "RT32-YU23-RE435",
     * "transactionDate": "09/09/2021",
     * "narrations": "Money for contributions",
     * "accountName": "Adebami",
     * "accountNumber": "180761555156",
     * "transactionType": "",
     * "senderName":"",
     * "recipientName":"",
     * "instrumentNumber":"",
     * "sessionId":"",
     * "clientRequestRef":""
     * }
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        return $this->getUser()
            ->setTransactionType()
            ->handleRequest()
            ->sendResponse();
    }

    private function handleRequest()
    {
        $data = [
            "user_id" => $this->user->id,
            "amount" => $this->request['amount'],
            "reference" => $this->request['transactionReference'],
            "commission" => 0.00,
            "charge" => 0.00,
            "type" => $this->request['type'],
            'description' => $this->request['narrations'],
            'category' => 'WALLET_WITHDRAWAL',
            'status' => 'successful',
            'payload' => json_encode($this->request),
            'is_webhook' => true,
        ];

        $this->response = (new CreateTransaction($data))->execute();
        return $this;
    }

    private function setTransactionType()
    {
        switch (strtolower($this->request['TransactionType'])) {
            case 'credit':
            case 'reversal': {
                $this->request['type'] = 'credit';
                break;
            }
            case 'debit': {
                $this->request['type'] = 'debit';
                break;
            }
            default:
                break;
        }
        return $this;
    }

    private function getUser()
    {
        $this->user = User::query()->where('account_number', $this->request['accountNumber'])->first();
        return $this;
    }

    private function sendResponse()
    {
        return response()->json(['message' => 'received webhook Ok', 'data' => $this->response, 'success' => true ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE );
    }
}