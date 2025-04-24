<?php


namespace Transave\CommonBase\Actions\Flutterwave;




use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Helpers\FlutterwaveApiHelper;
use Transave\CommonBase\Helpers\SessionHelper;
use Transave\CommonBase\Http\Models\DebitCard;

class TokenizeDebitCard extends Action
{
    use SessionHelper;
    private $request, $validatedData, $card;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        $this->validateRequest();
        $this->setDebitCard();
        $this->setReference();
        $this->setTokenizeData();
        return $this->chargeDebitCard();
    }

    private function chargeDebitCard()
    {
        return (new FlutterwaveApiHelper([
            'method' => 'POST',
            'url' => '/tokenized-charges',
            'data' => $this->validatedData
        ]))->execute();
    }

    private function setReference()
    {
        $this->validatedData['tx_ref'] = $this->generateReference();
        return $this;
    }

    private function setDebitCard()
    {
        $this->card = DebitCard::query()->find($this->validatedData['card_id']);
        return $this;
    }

    private function setTokenizeData()
    {
        $this->validatedData['token'] = $this->card->token;
        $this->validatedData['currency'] = $this->card->currency;
        $this->validatedData['email'] = $this->card->email;
        return $this;
    }

    private function validateRequest() : self
    {
        $this->validatedData = $this->validate($this->request, [
            'card_id' => 'required|exists:debit_cards,id',
            'amount' => 'required|numeric|gt:0'
        ]);
        return $this;
    }
}