<?php


namespace Transave\CommonBase\Actions\DebitCard;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Http\Models\DebitCard;

class UpdateDebitCard extends Action
{
    private $request, $validatedData;
    private DebitCard $card;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        $this->validateRequest();
        $this->setDebitCard();
        return $this->updateDebitCard();

    }

    private function updateDebitCard()
    {
       $this->card->fill($this->validatedData)->save();
       return $this->sendSuccess($this->card->refresh()->load('user'), 'card updated');
    }

    private function setDebitCard()
    {
        $this->card = DebitCard::query()->find($this->validatedData['debit_card_id']);
        return $this;
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'debit_card_id' => 'required|exists:debit_cards,id',
            'email' => 'sometimes|required|email',
            'type' => 'sometimes|required|string|in:VERVE,MASTERCARD,VISACARD',
            'country' => 'nullable|size:2',
            'currency' => 'nullable|size:3',
            'is_third_party' => 'sometimes|required|in:yes,no'
        ]);
        return $this;
    }
}