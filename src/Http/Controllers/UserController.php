<?php


namespace Transave\CommonBase\Http\Controllers;


use Illuminate\Http\Request;
use Transave\CommonBase\Actions\Kuda\Account\UpdateVirtualAccount;
use Transave\CommonBase\Actions\User\UpdateWithdrawalLimit;

class UserController extends Controller
{
    /**
     * Update withdrawal limit
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|UpdateWithdrawalLimit
     */
    public function updateWithdrawalLimit(Request $request, $id)
    {
        return (new UpdateWithdrawalLimit([
            'user_id' => $id,
            'withdrawal_limit' => $request->get('withdrawal_limit')
        ]))->execute();
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
}