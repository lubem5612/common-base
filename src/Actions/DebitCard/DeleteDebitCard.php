<?php


namespace Transave\CommonBase\Actions\DebitCard;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Http\Models\DebitCard;

class DeleteDebitCard extends Action
{
    private DebitCard $debitCard;
    private $request, $validatedInput;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        return $this->validateRequest()->getDebitCard()->deleteDebitCard();
    }

    private function deleteDebitCard()
    {
        $this->debitCard->delete();
        return $this->sendSuccess(null, 'debit card deleted successfully');
    }

    private function getDebitCard()
    {
        $this->debitCard = DebitCard::query()->find($this->validatedInput['debit_card_id']);
        return $this;
    }

    private function validateRequest()
    {
        $this->validatedInput = $this->validate($this->request, [
            'debit_card_id' => 'required|exists:debit_cards,id'
        ]);
        return $this;
    }
}