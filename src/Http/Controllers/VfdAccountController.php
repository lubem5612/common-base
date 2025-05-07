<?php


namespace Transave\CommonBase\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Transave\CommonBase\Actions\VFD\Transfer\BankList;
use Transave\CommonBase\Actions\VFD\Transfer\AccountEnquiry;
use Transave\CommonBase\Actions\VFD\Transfer\BankTransfer;
use Transave\CommonBase\Actions\Kuda\Webhook\WebhookService;

/**
 * @group Kuda Transfer Controller Endpoints
 *
 * API routes for handling bank transfers though Kuda third party API
 */
class VfdAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('admin')->only('mainAccountTransfer');
    }

    /**
     * Get a listing of Kuda Banks
     *
     * @response [
     * {
     * "bankCode": "999129",
     * "bankName": "Kudimoney(Kudabank)"
     * },
     * {
     * "bankCode": "999044",
     * "bankName": "Access Bank"
     * },
     * {
     * "bankCode": "999057",
     * "bankName": "Zenith Bank"
     * }
     * ]
     * @return mixed
     */
    public function bankList()
    {
        $response = (new BankList())->execute();
        abort_unless($response['success'], 403, 'failed in fetching bank list');

        return $response['data']['bank'];
    }

    /**
     * Query the details of a beneficiary
     *
     * @bodyParam beneficiary_account_number string required The beneficiary account number. for test, use 2504193714
     * @bodyParam beneficiary_bank_code string required The beneficiary bank code obtained from kuda bank list. for test, use 999129
     * @response {
     * "data": {
     * "beneficiaryAccountNumber": "2504193714",
     * "beneficiaryName": "(Slait)-Stephen Francis",
     * "senderAccountNumber": null,
     * "senderName": null,
     * "beneficiaryCustomerID": 0,
     * "beneficiaryBankCode": "999129",
     * "nameEnquiryID": 0,
     * "responseCode": null,
     * "transferCharge": 0,
     * "sessionID": "NA"
     * },
     * "success": true,
     * "message": "Request successful.",
     * "meta_data": {
     * "requestRef": "transave-202401021835-k5d815ujr",
     * "serviceType": "NAME_ENQUIRY"
     * }
     * }
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|AccountEnquiry
     */
    public function nameEnquiry(Request $request)
    {
        $sender = $request->user();
        $senderAccount = null;
        foreach($sender->accounts as $account) {
            if ($account->bank_name == config('commonbase.vfd.bank_name')) {
                $senderAccount = $account;
                break;
            }
        }

        $bank = $request->query('beneficiary_bank_code');
        return (new AccountEnquiry([
            'accountNumber' => $senderAccount->account_number,
            "accountNo"     => $request->query('beneficiary_account_number'),
            "bank"          => $bank,
            "transferType" => ($bank == config('commonbase.vfd.bank_code')) ? 'intra' : 'inter',
        ]))->execute();
    }

    /**
     * Customer Account Fund Transfer
     *
     * @bodyParam user_id string required The primary key user ID of the sender. for test, use c0329e2b-67d8-4c30-8bd9-51208c0b5975
     * @bodyParam beneficiary_account_number string required The account number of the beneficiary. for test, use 2504193714
     * @bodyParam beneficiary_bank_code string required The bank code obtained from the kuda bank list for test, use 999129
     * @bodyParam beneficiary_name string required The name of the beneficiary. for test, use 'Stephen Francis'
     * @bodyParam amount integer required The amount to transfer in Naira
     * @bodyParam narration string required A description of the transaction
     * @bodyParam name_enquiry_sessionID string required The session ID obtained in beneficiary details endpoint. for test use 'NA'
     * @bodyParam client_fee_charge intThe optional amount to charge for the transaction
     * @response {
     * "data": null,
     * "success": true,
     * "message": "Transaction successful",
     * "meta_data": {
     * "requestRef": "transave-202401021906-dax2ugaus",
     * "serviceType": "VIRTUAL_ACCOUNT_FUND_TRANSFER"
     * }
     * }
     * @param Request $request
     */
    public function fundTransfer(Request $request)
    {
        return (new BankTransfer($request->all()))->execute();
    }

    public function webhook(Request $request)
    {
        $response = (new WebhookService($request))->execute();
        Log::info($response);
    }
}
