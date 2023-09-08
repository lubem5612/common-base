<?php


namespace Transave\CommonBase\Actions\Kuda\Account;


use Transave\CommonBase\Helpers\KudaApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;

class DisableVirtualAccount
{
    use ResponseHelper, ValidationHelper;

    private $request, $validatedData;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            return $this->validateRequest()->disableVirtualAccount();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function disableVirtualAccount()
    {
        return (new KudaApiHelper(['serviceType' => 'ADMIN_DISABLE_VIRTUAL_ACCOUNT', 'data' => $this->validatedData]))->execute();
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