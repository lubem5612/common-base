<?php


namespace Transave\CommonBase\Actions\Transaction;


use Carbon\Carbon;
use Illuminate\Support\Str;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Http\Models\FailedTransaction;
use Transave\CommonBase\Http\Models\Transaction;

class CreateTransaction extends Action
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
                ->setReference()
                ->setStatus()
                ->setJsonPayload()
                ->createTransaction();
        }catch (\Exception $e) {
            FailedTransaction::query()->create([
                'user_id' => $this->validatedData['user_id'],
                'payload' => json_encode($this->validatedData)
            ]);
            return $this->sendServerError($e);
        }
    }

    private function createTransaction()
    {
        $transaction = Transaction::query()->create($this->validatedData);
        return $this->sendSuccess($transaction, 'transaction created successfully');
    }

    private function setStatus()
    {
        if (!array_key_exists('status', $this->validatedData)) {
            $this->validatedData['status'] = 'pending';
        }
        return $this;
    }

    private function setJsonPayload()
    {
        if (array_key_exists('payload', $this->validatedData)) {
            if (!$this->isJsonValidated($this->validatedData['payload']))
                $this->validatedData['payload'] = json_encode($this->validatedData['payload']);
        }
        return $this;
    }

    private function setReference()
    {
        if (!array_key_exists('reference', $this->validatedData)) {
            $this->validatedData['reference'] = 'transave-'.Carbon::now()->format('YmdHi').'-'.strtolower(Str::random(9));
        }
        return $this;
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'user_id' => 'required|exists:users,id',
            'reference' => 'nullable|string|min:20',
            'amount' => 'required|numeric|gt:0',
            'charges' => 'nullable|numeric|gte:0',
            'commission' => 'nullable|numeric|gte:0',
            'type' => 'required|string|in:debit,credit',
            'description' => 'nullable|string|max:700',
            'category' => 'required|string',
            'status' => 'nullable|string',
            'payload' => 'nullable',
        ]);
        return $this;
    }
}