<?php


namespace Transave\CommonBase\Actions\Flutterwave;


use Transave\CommonBase\Actions\Transaction\CreateTransaction;
use Transave\CommonBase\Helpers\FlutterwaveApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;
use Transave\CommonBase\Http\Models\DebitCard;

class VerifyAndCreateTransaction
{
    use ValidationHelper, ResponseHelper;
    private $request, $validatedData, $response = [];

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            $this->validateRequest();
            $this->queryTransactionStatus();
            $this->createCardDetails();
            return $this->createTransaction();
        }catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    private function createCardDetails()
    {
        DebitCard::query()->create([
            'user_id' => auth()->id(),
            'first_digits' => $this->response['card']['first_6digits'],
            'last_digits' => $this->response['card']['last_4digits'],
            'issuer' => $this->response['card']['issuer'],
            'email' => $this->response['customer']['email'],
            'type' => $this->response['card']['type'],
            'is_third_party' => 'yes',
            'expiry' => $this->response['card']['expiry'],
            'token' => $this->response['card']['token']
        ]);
    }

    private function createTransaction()
    {
        $response = (new CreateTransaction([
            'user_id' => auth()->id(),
            'reference' => $this->validatedData['tx_ref'],
            'amount' => $this->response['amount'],
            'charges' => $this->response['app_fee'],
            'commission' => 0.00,
            'type' => 'debit',
            'description' => $this->validatedData['description'],
            'category' => 'FLUTTERWAVE',
            'status' => 'successful',
            'payload' => json_encode($this->response),
        ]))->execute();
        return $this->sendSuccess($response, 'transaction verified and created successfully');
    }

    private function queryTransactionStatus()
    {
        $this->response = (new FlutterwaveApiHelper([
            'method' => 'GET',
            'url' => '/transactions/verify_by_reference?tx_ref='.$this->validatedData['tx_ref']
        ]))->execute();

        if (!$this->response['success']) {
            abort(404, 'unable to verify transaction');
        }
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'tx_ref' => 'required|string|min:15',
            'description' => 'required|string',
        ]);
    }
}