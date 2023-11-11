<?php


namespace Transave\CommonBase\Actions\Flutterwave;


use Illuminate\Support\Arr;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Actions\DebitCard\CreateDebitCard;
use Transave\CommonBase\Helpers\FlutterwaveApiHelper;
use Transave\CommonBase\Helpers\SessionHelper;
use Transave\CommonBase\Http\Models\User;

class InitiateCardTransaction extends Action
{
    use SessionHelper;
    private $request, $validatedData, $chargeCard;
    private $user;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        return $this
            ->validateRequest()
            ->setUser()
            ->setReference()
            ->setEmail()
            ->setCurrency()
            ->setRedirectUrl()
            ->chargeCard()
            ->tokenizeCard()
            ->sendSuccess($this->chargeCard, 'card transaction initiated successfully');
    }

    private function chargeCard()
    {
        $this->chargeCard = (new FlutterwaveApiHelper([
            'method' => 'POST',
            'url' => '/charges?type=card',
            'data' => $this->validatedData,
        ]))->execute();
        return $this;
    }

    private function setUser()
    {
        if (Arr::exists($this->validatedData, 'user_id') && $this->validatedData['user_id']) {
            $this->user = User::query()->find($this->validatedData['user_id']);
        }else {
            $this->user = auth()->user();
        }
        return $this;
    }

    private function setCurrency()
    {
        if (!array_key_exists('currency', $this->validatedData)) {
            $this->validatedData['currency'] = "NGN";
        }
        return $this;
    }

    private function setReference()
    {
        $this->validatedData['tx_ref'] = $this->generateReference();
        return $this;
    }

    private function setRedirectUrl()
    {
        $this->validatedData['redirect_url'] = route('flutterwave.redirect', ['id' => $this->user->id]);
        return $this;
    }

    private function setEmail()
    {
        if (!array_key_exists('email', $this->validatedData)) {
            $this->validatedData['email'] = $this->user->email;
        }
        return $this;
    }

    private function tokenizeCard()
    {
        $response = (new CreateDebitCard([
            'user_id' => $this->user->id,
            'first_digits' => $this->chargeCard['data']['card']['first_6digits'],
            'last_digits' => $this->chargeCard['data']['card']['last_4digits'],
            'issuer' => $this->chargeCard['data']['card']['issuer'],
            'email' => $this->chargeCard['data']['customer']['email'],
            'type' => $this->chargeCard['data']['card']['type'],
            'country' => $this->chargeCard['data']['card']['country'],
            'is_third_party' => 'yes',
            'expiry' => $this->chargeCard['data']['card']['expiry'],
            'token' => $this->chargeCard['data']['card']['token']
        ]))->execute();

        abort_unless($response['success'], 403, 'unable to create card');
        return $this;
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "card_number" => "required|numeric",
            "cvv" => "required|string|size:3",
            "expiry_month" => "required|string|size:2",
            "expiry_year" => "required|string|size:2",
            "currency" => "nullable",
            "amount" => "required|numeric|gt:0",
            "fullname" => "required|string",
            "email" => "nullable",
            "user_id" => "nullable"
        ]);
        return $this;
    }
}