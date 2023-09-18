<?php


namespace Transave\CommonBase\Http\Controllers;


use Illuminate\Http\Request;
use Transave\CommonBase\Actions\Auth\Login;
use Transave\CommonBase\Actions\Auth\ResendToken;
use Transave\CommonBase\Actions\Auth\VerifyAccount;
use Transave\CommonBase\Actions\Kuda\Account\CreateVirtualAccount;
use Transave\CommonBase\Helpers\ResponseHelper;

class AuthController extends Controller
{
    use ResponseHelper;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['user', 'logout']);
    }

    /**
     * Register a new account on kuda and transave
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        return (new CreateVirtualAccount($request->all()))->execute();
    }

    /**
     * Login to existing account
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        return (new Login([
            'username' => $request->get('username'),
            'password' => $request->get('password')
        ]))->execute();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Transave\CommonBase\Actions\Action
     */
    public function resendToken(Request $request)
    {
        return (new ResendToken(['email' => $request->get('email')]))->execute();
    }

    /**
     * Verify registered account
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|VerifyAccount
     */
    public function verifyAccount(Request $request)
    {
        return (new VerifyAccount(['verification_token' => $request->get('token')]))->execute();
    }

    /**
     * Get authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function user(Request $request)
    {
        try {
            $user = $request->user();
            return $this->sendSuccess($user, "user retrieved successfully");
        } catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    /**
     * Log out authenticated user
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return $this->sendSuccess(null, 'user logged out successfully');
    }
}