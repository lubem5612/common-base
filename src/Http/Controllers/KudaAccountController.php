<?php


namespace Transave\CommonBase\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Transave\CommonBase\Actions\Kuda\Transfer\BankList;
use Transave\CommonBase\Actions\Kuda\Transfer\IntrabankFundTransfer;
use Transave\CommonBase\Actions\Kuda\Transfer\MainAccountFundTransfer;
use Transave\CommonBase\Actions\Kuda\Transfer\NameEnquiry;
use Transave\CommonBase\Actions\Kuda\Transfer\VirtualAccountFundTransfer;
use Transave\CommonBase\Actions\Kuda\Webhook\WebhookService;

class KudaAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('admin')->only('mainAccountTransfer');
    }

    public function bankList()
    {
        $response = (new BankList())->execute();
        abort_unless($response['success'], 403, 'failed in fetching bank list');

        return $response['data']['banks'];
    }

    public function nameEnquiry(Request $request)
    {
        $response = (new NameEnquiry([
            "beneficiary_account_number"    => $request->get('beneficiary_account_number'),
            "beneficiary_bank_code"         => $request->get('beneficiary_bank_code'),
            "user_id"                       => $request->get('sender_user_id'),
        ]))->execute();

        return $response;
    }

    public function virtualAccountTransfer(Request $request)
    {
        $response = (new VirtualAccountFundTransfer($request->all()))->execute();

        return $response;
    }

    public function mainAccountTransfer(Request $request)
    {
        $response = (new MainAccountFundTransfer($request->all()))->execute();
        return $response;
    }

    public function walletTransfer(Request $request)
    {
        $response = (new IntrabankFundTransfer([
            'beneficiary_user_id' => $request->get('beneficiary_user_id'),
            'amount' => $request->get('amount'),
            'narration' => $request->get('narration')
        ]))->execute();

        return $response;
    }

    public function webhook(Request $request)
    {
        $response = (new WebhookService($request))->execute();
        Log::info($response);
    }
}
