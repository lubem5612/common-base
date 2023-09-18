<?php


namespace Transave\CommonBase\Actions\Transaction;


use Carbon\Carbon;
use Illuminate\Support\Str;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Http\Models\Transaction;

class UpdateTransaction extends Action
{
    private $request, $validatedData;
    private Transaction $transaction;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        return $this->validateRequest()
            ->setTransaction()
            ->setJsonPayload()
            ->updateTransaction();
    }

    private function updateTransaction()
    {
        $this->transaction->fill($this->validatedData)->save();
        return $this->sendSuccess($this->transaction->refresh(), 'transaction updated successfully');
    }

    private function setJsonPayload()
    {
        if (array_key_exists('payload', $this->validatedData)) {
            if (!$this->isJsonValidated($this->validatedData['payload']))
                $this->validatedData['payload'] = json_encode($this->validatedData['payload']);
        }
        return $this;
    }

    private function setTransaction()
    {
        $this->transaction = Transaction::query()->find($this->validatedData['transaction_id']);
        return $this;
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'transaction_id' => 'required|exists:transactions,id',
            'amount' => 'nullable|numeric|gt:0',
            'charges' => 'nullable|numeric|gte:0',
            'commission' => 'nullable|numeric|gte:0',
            'description' => 'nullable|string|max:700',
            'category' => 'nullable|string',
            'status' => 'nullable|string',
            'payload' => 'nullable',
        ]);
        return $this;
    }
}