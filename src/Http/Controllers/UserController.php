<?php


namespace Transave\CommonBase\Http\Controllers;


use Illuminate\Http\Request;
use Transave\CommonBase\Actions\Kuda\Account\UpdateVirtualAccount;
use Transave\CommonBase\Actions\User\ChangeEmail;
use Transave\CommonBase\Actions\User\ChangePassword;
use Transave\CommonBase\Actions\User\DeleteAccount;
use Transave\CommonBase\Actions\User\GetUserKyc;
use Transave\CommonBase\Actions\User\RestoreAccount;
use Transave\CommonBase\Actions\User\SearchUsers;
use Transave\CommonBase\Actions\User\SetTransactionPin;
use Transave\CommonBase\Actions\User\UpdateAccountStatus;
use Transave\CommonBase\Actions\User\UpdateAccountType;
use Transave\CommonBase\Actions\User\VerifyTransactionPin;
use Transave\CommonBase\Helpers\KycHelper;
use Transave\CommonBase\Http\Models\User;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Update user records
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = array_merge($request->all(), ['user_id' => $id]);
        return (new UpdateVirtualAccount($data))->execute();
    }

    /**
     * Change authenticated user email
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|ChangeEmail
     */
    public function changeEmail(Request $request)
    {
        return (new ChangeEmail(['email' => $request->get('email')]))->execute();
    }

    /**
     * Change account password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|ChangePassword
     */
    public function changePassword(Request $request)
    {
        return (new ChangePassword($request->all()))->execute();
    }

    /**
     * Get a listing of users
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        $relationship = ['kyc'];
        $wallet = request()->query('with_wallet');
        if (isset($wallet)) {
            array_push($relationship, 'wallet');
        }
        return (new SearchUsers(User::class, $relationship))->execute();
    }

    /**
     * Set authenticated user transaction PIN
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|SetTransactionPin
     */
    public function setPin(Request $request)
    {
        return (new SetTransactionPin(['transaction_pin' => $request->get('transaction_pin')]))->execute();
    }

    /**
     * Verify users PIN
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|VerifyTransactionPin
     */
    public function verifyPin(Request $request, $id)
    {
        return (new VerifyTransactionPin([
            'user_id' => $id,
            'transaction_pin' => $request->get('pin')
        ]))->execute();
    }

    /**
     * Update account status
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|UpdateAccountStatus
     */
    public function updateAccountStatus(Request $request, $id)
    {
        return (new UpdateAccountStatus([
            'user_id' => $id,
            'account_status' => $request->get('account_status')
        ]))->execute();
    }

    /**
     * Update account type
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|UpdateAccountType
     */
    public function updateAccountType(Request $request, $id)
    {
        return (new UpdateAccountType([
            'user_id' => $id,
            'account_type' => $request->get('account_type')
        ]))->execute();
    }

    /**
     * Soft delete and deactivate account
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|DeleteAccount
     */
    public function delete(Request $request)
    {
        return (new DeleteAccount(['user_id' => $request->get('user_id')]))->execute();
    }

    /**
     * Restore Soft deleted account
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|RestoreAccount
     */
    public function restore(Request $request)
    {
        return (new RestoreAccount(['user_id' => $request->get('user_id')]))->execute();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function kyc($id)
    {
        return (new GetUserKyc(['user_id' => $id]))->execute();
    }
}