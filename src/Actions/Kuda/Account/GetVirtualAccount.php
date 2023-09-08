<?php


namespace Transave\CommonBase\Actions\Kuda\Account;



use Transave\CommonBase\Helpers\KudaApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;

class GetVirtualAccount
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
            return $this->validateRequest()->getVirtualAccount();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function getVirtualAccount()
    {
        return (new KudaApiHelper(['serviceType' => 'ADMIN_RETRIEVE_SINGLE_VIRTUAL_ACCOUNT', 'data' => $this->validatedData]))->execute();
    }

    private function validateRequest()
    {
        $data = $this->validate($this->request, [
            "user_id" => 'required|exists:users,id'
        ]);
        $this->validatedData['trackingReference'] = $data['user_id'];
        return $this;
    }
}