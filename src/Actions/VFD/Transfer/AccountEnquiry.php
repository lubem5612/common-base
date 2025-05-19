<?php


namespace Transave\CommonBase\Actions\VFD\Transfer;



use Transave\CommonBase\Helpers\VfdApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;

class AccountEnquiry
{
    use ResponseHelper, ValidationHelper;

    private $validatedData, $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            return $this->validateRequest()->getSubAccount();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function getSubAccount()
    {
        $accountNumber = $this->validatedData['accountNumber'];
        $endpoint = "/account/enquiry?accountNumber=$accountNumber";
        $account = (new VfdApiHelper([], $endpoint, 'get'))->execute();

        if (array_key_exists('accountNo', $this->validatedData)) {
            $accountNo = $this->validatedData['accountNo'];
            $bank = $this->validatedData['bank'];
            $tranfer_type = $this->validatedData['transfer_type'];
            $endpoint = "/transfer/recipient?accountNo=$accountNo&bank=$bank&transfer_type=$tranfer_type";
            $beneficiary = (new VfdApiHelper([], $endpoint, 'get'))->execute();

            $data = [
                'sender'        => $account,
                'beneficiary'   => $beneficiary
            ];

            return $this->sendSuccess($data, 'Sender and beneficiary details retrieved');
        }

        return $account;
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'accountNumber' => 'required|string|exists:account_numbers,account_number',
            'accountNo'     => 'numeric|required_if:transferType,!=,null',
            'bank'          => 'numeric|required_if:transferType,!=,null',
            'transferType'  => 'string|required_if:accountNo,!=,null'
        ]);

        $this->validatedData['transfer_type'] = $this->validatedData['transferType'];
    
        return $this;
    }
}
