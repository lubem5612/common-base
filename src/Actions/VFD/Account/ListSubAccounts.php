<?php


namespace Transave\CommonBase\Actions\VFD\Account;


use Transave\CommonBase\Helpers\VfdApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;

class ListSubAccounts
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
            return $this
                ->validateRequest()
                ->setPageNumber()
                ->setPageSize()
                ->setEntity()
                ->listSubAccounts();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function listSubAccounts()
    {
        $entity = $this->validatedData['entity'];
        $page = $this->validatedData['pageNumber'];
        $size = $this->validatedData['pageSize'];
        $endpoint = "/sub-accounts?entity=$entity&size=$size&page=$page";
        $response = (new VfdApiHelper([], $endpoint, 'get'))->execute();

        if (!isset($response['data']['content'])) return $response;

        $accounts = $response['data']['content'];
        unset($response['data']['content']);
        $rest = $response['data'];
        unset($response['data']);
        return [
            'accounts' => $accounts,
            ...$rest,
            ...$response
        ];
    }

    private function setPageSize()
    {
        if (!array_key_exists('pageSize', $this->validatedData)) $this->validatedData['pageSize'] = 10;
        $this->validatedData['pageSize'] = (string)$this->validatedData['pageSize'];
        return $this;
    }

    private function setPageNumber()
    {
        if (!array_key_exists('pageNumber', $this->validatedData)) $this->validatedData['pageNumber'] = 0;
        $this->validatedData['pageNumber'] = (string)$this->validatedData['pageNumber'];
        return $this;
    }

    private function setEntity()
    {
        if (!array_key_exists('entity', $this->validatedData)) $this->validatedData['entity'] = 'individual';
        return $this;
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'pageSize'      => 'nullable|numeric|gte:0',
            'pageNumber'    => 'nullable|numeric|gte:0',
            'entity'        => 'nullable|string'
        ]);
        return $this;
    }
}
