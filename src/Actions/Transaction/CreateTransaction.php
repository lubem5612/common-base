<?php


namespace Transave\CommonBase\Actions\Transaction;


use Carbon\Carbon;
use Illuminate\Support\Str;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Http\Models\Transaction;

class CreateTransaction extends Action
{
    private $request, $validatedData;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        return $this->validateRequest()
            ->setReference()
            ->setStatus()
            ->setJsonPayload()
            ->createTransaction();
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
        $this->validatedData['reference'] = 'transave-'.Carbon::now()->format('YmdHi').'-'.strtolower(Str::random(9));
        return $this;
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'user_id' => 'required|exists:users,id',
            'reference' => 'nullable|string|min:20',
            'amount' => 'required|numeric|gt:0',
            'charges' => 'nullable|numeric|gt:0',
            'commission' => 'nullable|numeric|gt:0',
            'type' => 'required|string|in:debit,credit',
            'description' => 'nullable|string|max:700',
            'category' => 'required|string',
            'status' => 'nullable|string',
            'payload' => 'nullable',
        ]);
        return $this;
    }
}