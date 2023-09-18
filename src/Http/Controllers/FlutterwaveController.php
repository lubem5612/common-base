<?php


namespace Transave\CommonBase\Http\Controllers;


use Illuminate\Http\Request;
use Transave\CommonBase\Actions\Flutterwave\FlutterwaveBankList;
use Transave\CommonBase\Actions\Flutterwave\InitiateBankTransfer;
use Transave\CommonBase\Actions\Flutterwave\InitiateCardTransaction;
use Transave\CommonBase\Actions\Flutterwave\TokenizeDebitCard;
use Transave\CommonBase\Actions\Flutterwave\ValidateCharge;

class FlutterwaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['bankList', 'redirect']);
    }

    public function redirect($id)
    {

    }

    public function bankList()
    {
        $response = (new FlutterwaveBankList(['country' => 'NG']))->execute();
        abort_unless($response['success'], 403, 'failed in fetching bank list');
        return $response;
    }

    public function initiateBankTransfer(Request $request)
    {
        $response = (new InitiateBankTransfer($request->all()))->execute();
        abort_unless($response['success'], 403, 'failed in initiating bank transfer');
        return $response;
    }

    public function initiateCardTransaction(Request $request)
    {
        $response = (new TokenizeDebitCard($request->all()))->execute();
        abort_unless($response['success'], 403, 'failed in initiating card transaction');
        return $response;
    }

    public function chargeReturningCustomer(Request $request)
    {
        $response = (new InitiateCardTransaction([
            'user_id' => $request->get('user_id'),
            'amount' => $request->get('amount')
        ]))->execute();
        abort_unless($response['success'], 403, 'failed in tokenizing debit card');
        return $response;
    }

    public function validateCharge(Request $request)
    {
        $response = (new ValidateCharge([
            "otp" => $request->get('otp'),
            "flw_ref" => $request->get('flw_ref'),
            "type" =>$request->get('type')
        ]))->execute();
        abort_unless($response['success'], 403, 'failed in validating transaction');
        return $response;
    }


}