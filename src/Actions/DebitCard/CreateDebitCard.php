<?php


namespace Transave\CommonBase\Actions\DebitCard;


use Carbon\Carbon;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Http\Models\DebitCard;

class CreateDebitCard extends Action
{
    private $request, $validatedData;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        $this->validateRequest();
        $this->getLastForDigits();
        $this->getFirstSixDigits();
        $this->setCurrency();
        $this->setCountry();
        $this->setEmail();
        $this->setThirdParty();
        $this->setExpiry();
        return $this->createDebitCard();

    }

    private function createDebitCard()
    {
        $doesNotExist = DebitCard::query()
            ->where('user_id', $this->validatedData['user_id'])
            ->where('first_digits', $this->validatedData['first_digits'])
            ->where('last_digits', $this->validatedData['last_digits'])
            ->where('email', $this->validatedData['email'])
            ->doesntExist();
        if ($doesNotExist) {
            $card = DebitCard::query()->create($this->validatedData);
            return $this->sendSuccess($card, 'card created successfully');
        }
        return $this->sendError('card already exists');
    }

    private function getLastForDigits()
    {
        if (array_key_exists('card_number', $this->validatedData)) {
            $string = $this->structureCardNumber($this->validatedData['card_number']);
            $this->validatedData['last_digits'] = substr($string, -4);
        }
        return $this;
    }

    private function getFirstSixDigits()
    {
        if (array_key_exists('card_number', $this->validatedData)) {
            $string = $this->structureCardNumber($this->validatedData['card_number']);
            $this->validatedData['first_digits'] = substr($string, 0, 6);
        }
        return $this;
    }

    private function structureCardNumber($number)
    {
        $string = (string)$number;
        $string = str_replace(' ', '', $string);
        $string = str_replace('-', '', $string);
        return trim($string);
    }

    private function setEmail()
    {
        if (!array_key_exists('email', $this->validatedData)) {
            $this->validatedData['email'] = auth()->user()->email;
        }
        return $this;
    }

    private function setCurrency()
    {
        if (!array_key_exists('currency', $this->validatedData)) {
            $this->validatedData['currency'] = 'NGN';
        }
        return $this;
    }

    private function setCountry()
    {
        if (!array_key_exists('country', $this->validatedData)) {
            $this->validatedData['country'] = 'NG';
        }
        return $this;
    }

    private function setThirdParty()
    {
        if (!array_key_exists('is_third_party', $this->validatedData)) {
            $this->validatedData['is_third_party'] = 'no';
        }
        return $this;
    }

    private function setExpiry()
    {
        $this->validatedData['expiry'] = Carbon::parse($this->validatedData['expiry'])->format('m/YY');
        return $this;
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'user_id' => 'required|exists:users',
            'first_digits' => 'nullable',
            'last_digits' => 'nullable',
            'issuer' => 'nullable|string|max:150',
            'email' => 'required|email',
            'type' => 'required|string|in:VERVE,MASTERCARD,VISACARD',
            'country' => 'nullable|size:2',
            'currency' => 'nullable|size:3',
            'is_third_party' => 'required|in:yes,no',
            'expiry' => 'required|string|max:6',
            'token' => 'nullable|string|max:255'
        ]);
        return $this;
    }
}