<?php


namespace Transave\CommonBase\Http\Controllers;


use Illuminate\Http\Request;
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
}