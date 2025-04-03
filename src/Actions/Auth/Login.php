<?php


namespace Transave\CommonBase\Actions\Auth;



use Illuminate\Support\Str;
use Transave\CommonBase\Actions\User\GetUserKyc;
use Transave\CommonBase\Helpers\PhoneHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;
use Transave\CommonBase\Http\Models\User;

class Login
{
    use ResponseHelper, PhoneHelper, ValidationHelper;
    private array $request;
    private array $validatedData;
    private $kyc = null;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            return $this->validateRequest()->login();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function username()
    {
        $input = $this->validatedData['username'];
        if(filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        } elseif ($this->isPhoneNumber($input)) {
            $this->validatedData['username'] = $this->getInternationalNumber($input);
            return 'phone';
        }else{
            return 'bvn';
        }
    }

    private function isPhoneNumber($phone)
    {
        $int_number = Str::start($phone, '+');
        $number_clean_up = str_replace('-', '', substr($int_number, 1));
        $number_trim = str_replace(' ', '', $number_clean_up);
        if (strlen($number_clean_up) >= 8) {
            return is_numeric($number_trim);
        }
        return false;
    }

    private function login()
    {
        if (auth()->attempt([$this->username() => $this->validatedData['username'], 'password' => $this->validatedData['password']])) {
            $user = auth()->user();
            $this->getUserKyc();
            $user->tokens()->delete();

            $user['token'] = $user->createToken(uniqid())->plainTextToken;
            $user['account'] = $this->kyc;
            $user['url'] = $this->redirectUrl();

            return $this->sendSuccess( $user, 'user logged in successfully.');
        } elseif (User::query()->where('email', $this->validatedData['username'])
            ->orWhere('phone', $this->validatedData['username'])
            ->orWhere('bvn', $this->validatedData['username'])->exists()) {
            return $this->sendError( 'password did not match username', ['errors' => ['password or username dont match']], 401);
        } else {
            return $this->sendError('entry details does not exist', ['errors' => ['record does not exist']], 404);
        }
    }

    private function getUserKyc()
    {
        $response = (new GetUserKyc(['user_id' => auth()->id()]))->execute();
        $response = json_decode($response->getContent(), true);
        if ($response['success']) $this->kyc = $response['data'];
    }

    private function redirectUrl()
    {
        switch (auth()->user()->role) {
            case "customer":
                return "/customer";
            case "staff":
                return "/staff";
            case "admin":
                return "/admin";
            default:
                return "/";
        }
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'username' => 'required',
            'password' => 'required'
        ]);
        return $this;
    }
}