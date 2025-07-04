<?php

namespace Transave\CommonBase\Actions\Transaction;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Http\Models\Transaction;

class GetTransactions extends Action
{
    private $request, $validatedData;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            return $this->validateRequest()
                ->getTransactions();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function getTransactions()
    {
        $user_id = $this->validatedData['user_id'];
        $limit = $this->validatedData['limit'] ?? 10;
        $page = $this->validatedData['page'];
        
        $builder = Transaction::query()->where([
            'user_id' => $user_id
        ])->latest();

        $transactions = $page
            ? $builder->limit($limit)->paginate($limit)
            : $builder->limit($limit)->get();

        $successfulTransactions = $transactions->where('status', 'successful');
        $pendingTransactions = $transactions->where('status', '!=', 'successful')->where('status', '!=', 'failed');
        $failedTransactions = $transactions->where('status', 'failed');

        $data = [
            'transactions'                  => $transactions,
            'totalTransactions'             => $transactions->count(),
            'totalTransactionsAmount'       => $transactions->sum('amount'),
            'successfulTransactions'        => $successfulTransactions->count(),
            'successfulTransactionsAmount'  => $successfulTransactions->sum('amount'),
            'pendingTransactions'           => $pendingTransactions->count(),
            'pendingTransactionsAmount'     => $pendingTransactions->sum('amount'),
            'failedTransactions'            => $failedTransactions->count(),
            'failedTransactionsAmount'      => $failedTransactions->sum('amount')
        ];
        
        return $this->sendSuccess($data, 'All transactions');
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'user_id'   => 'required|exists:users,id',
            'search'    => 'nullable|string',
            'limit'     => 'nullable|integer',
            'page'      => 'nullable|integer'
        ]);
        return $this;
    }
}
