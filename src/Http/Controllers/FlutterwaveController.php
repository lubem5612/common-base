<?php


namespace Transave\CommonBase\Http\Controllers;


use Illuminate\Http\Request;
use Transave\CommonBase\Actions\Flutterwave\FlutterwaveBankList;
use Transave\CommonBase\Actions\Flutterwave\InitiateBankTransfer;
use Transave\CommonBase\Actions\Flutterwave\InitiateCardTransaction;
use Transave\CommonBase\Actions\Flutterwave\TokenizeDebitCard;
use Transave\CommonBase\Actions\Flutterwave\ValidateCharge;
use Transave\CommonBase\Actions\Flutterwave\VerifyAndCreateTransaction;

class FlutterwaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['bankList', 'redirect']);
    }

    public function redirect($id)
    {

    }

    /**
     * Get a listing of Flutterwave banks
     *
     * @response {
     * "success": true,
     * "message": "Banks fetched successfully",
     * "data": [
     * {
     * "id": 132,
     * "code": "560",
     * "name": "Page MFBank"
     * },
     * {
     * "id": 133,
     * "code": "304",
     * "name": "Stanbic Mobile Money"
     * },
     * {
     * "id": 2333,
     * "code": "999999",
     * "name": "NIP Virtual Bank"
     * }
     * ]
     * }
     * @return \Illuminate\Http\JsonResponse|FlutterwaveBankList
     */
    public function bankList()
    {
        $response = (new FlutterwaveBankList(['country' => 'NG']))->execute();
        if (!$response['success']) {
            abort(403, 'failed in fetching bank list');
        }
        return $response;
    }

    public function initiateBankTransfer(Request $request)
    {
        $response = (new InitiateBankTransfer($request->all()))->execute();
        if (!$response['success']) {
            abort(403, 'failed in initiating bank transfer');
        }
        return $response;
    }

    public function initiateCardTransaction(Request $request)
    {
        $response = (new TokenizeDebitCard($request->all()))->execute();
        if (!$response['success']) {
            abort(403, 'failed in initiating card transaction');
        }
        return $response;
    }

    public function chargeReturningCustomer(Request $request)
    {
        $response = (new InitiateCardTransaction([
            'user_id' => $request->get('user_id'),
            'amount' => $request->get('amount')
        ]))->execute();
        if (!$response['success']) {
            abort(403, 'failed in tokenizing debit card');
        }
        return $response;
    }

    public function validateCharge(Request $request)
    {
        $response = (new ValidateCharge([
            "otp" => $request->get('otp'),
            "flw_ref" => $request->get('flw_ref'),
            "type" =>$request->get('type')
        ]))->execute();
        if (!$response['success']) {
            abort(403, 'failed in validating transaction');
        }
        return $response;
    }

    public function createTransaction(Request $request)
    {
        return (new VerifyAndCreateTransaction($request->all()))->execute();
    }
}