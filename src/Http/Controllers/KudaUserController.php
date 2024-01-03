<?php


namespace Transave\CommonBase\Http\Controllers;


use Illuminate\Http\Request;
use Transave\CommonBase\Actions\Kuda\Account\GetVirtualAccount;
use Transave\CommonBase\Actions\Kuda\Account\ListVirtualAccounts;
use Transave\CommonBase\Actions\Kuda\Account\MainAccountBalance;
use Transave\CommonBase\Actions\Kuda\Account\VirtualAccountBalance;

class KudaUserController extends Controller
{
    /**
     * KudaUserController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('admin')->only(['getMainAccountBalance', 'listVirtualAccount']);
    }

    public function getWalletBalance($id)
    {
        return (new VirtualAccountBalance(['user_id' => $id]))->execute();
    }

    public function getMainAccountBalance()
    {
        return (new MainAccountBalance())->execute();
    }

    public function listVirtualAccount()
    {
        return (new ListVirtualAccounts([
            'PageSize' => request()->query('per_page'),
            'PageNumber' => request()->query('page'),
        ]))->execute();
    }

    public function getVirtualAccountDetails($id)
    {
        return (new GetVirtualAccount(['user_id' => $id]))->execute();
    }
}