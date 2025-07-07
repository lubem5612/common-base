<?php


namespace Transave\CommonBase\Http\Controllers;


use Illuminate\Http\Request;
use Transave\CommonBase\Actions\Auth\ForgotPassword;
use Transave\CommonBase\Actions\Auth\ResetPassword;
use Transave\CommonBase\Actions\User\VerifyPassword;

class PasswordController extends Controller
{
    /**
     * verify password matches
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|VerifyPassword
     */
    public function verifyPassword(Request $request, $id)
    {
        return (new VerifyPassword(['user_id' => $id, 'password' => $request->get('password')]))->execute();
    }

    /**
     * Send password forgot request using user email
     * Token generated and sent to user phone number
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|ForgotPassword
     */
    public function forgotPassword(Request $request)
    {
        return (new ForgotPassword(['phone' => $request->get('phone')]))->execute();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|ResetPassword
     */
    public function resetPassword(Request $request)
    {
        return (new ResetPassword($request->all()))->execute();
    }
}
