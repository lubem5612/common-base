<?php


namespace Transave\CommonBase\Http\Controllers;


use Illuminate\Http\Request;
use Transave\CommonBase\Actions\Kuda\Account\GetVirtualAccount;
use Transave\CommonBase\Actions\VFD\Account\ListSubAccounts;
use Transave\CommonBase\Actions\VFD\Account\MainAccountBalance;
use Transave\CommonBase\Actions\Kuda\Account\VirtualAccountBalance;

/**
 * @group Kuda Users Controller Endpoints
 *
 * API routes for handling kuda account users
 */
class VfdUserController extends Controller
{
    /**
     * KudaUserController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('admin')->only(['getMainAccountBalance', 'listSubAccounts']);
    }

    /**
     * Get wallet balance of specified user
     *
     * @urlParam id required string The primary key User ID of the client
     * @response {
     * "data": {
     * "ledgerBalance": 4243200,
     * "availableBalance": 4243200,
     * "withdrawableBalance": 4243200
     * },
     * "success": true,
     * "message": "Operation successful",
     * "meta_data": {
     * "requestRef": "transave-202401031132-3tgioqz7h",
     * "serviceType": "RETRIEVE_VIRTUAL_ACCOUNT_BALANCE"
     * }
     * }
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function getWalletBalance($id)
    {
        return (new VirtualAccountBalance(['user_id' => $id]))->execute();
    }

    /**
     * Get the platform main account balance
     *
     * @response {
     * "data": {
     * "ledgerBalance": 4839904042612,
     * "availableBalance": 4839904042612,
     * "withdrawableBalance": 4839904042612
     * },
     * "success": true,
     * "message": "Operation successful",
     * "meta_data": {
     * "requestRef": "transave-202401031138-cqapf8tvk",
     * "serviceType": "ADMIN_RETRIEVE_MAIN_ACCOUNT_BALANCE"
     * }
     * }
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function getMainAccountBalance()
    {
        return (new MainAccountBalance())->execute();
    }

    /**
     * Get a listing of virtual accounts (Paginated)
     *
     * @queryParam page int The page number to return data (Paginated)
     * @queryParam limit int The number of rows to return
     * @queryParam entity string The type of accounts virtual|individual|corporate
     * @response {
     * "data": {
     * "accounts": [
     * {
     * "accountNumber": "2504191662",
     * "email": "admin@kuda.com",
     * "phoneNumber": "08037565343",
     * "lastName": "Kuda",
     * "firstName": "Krendo",
     * "middleName": null,
     * "bussinessName": null,
     * "accountName": "(Slait)-Kuda Krendo",
     * "trackingReference": "9992497871",
     * "creationDate": "2023-03-25T23:28:54.0766667",
     * "isDeleted": false
     * },
     * {
     * "accountNumber": "2504193714",
     * "email": "francis@example.com",
     * "phoneNumber": "08038602189",
     * "lastName": "Stephen",
     * "firstName": "Francis",
     * "middleName": null,
     * "bussinessName": null,
     * "accountName": "(Slait)-Stephen Francis",
     * "trackingReference": "e5951615-3f2c-4903-962f-d2cfdabd10cb",
     * "creationDate": "2023-03-31T19:04:04.1333333",
     * "isDeleted": false
     * },
     * ],
     * "totalCount": 12
     * },
     * "success": true,
     * "message": "Request successful.",
     * "meta_data": {
     * "requestRef": "transave-202401031154-qnytehuht",
     * }
     * }
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function listSubAccounts(Request $request)
    {
        return (new ListSubAccounts([
            'pageSize'      => $request->query('limit'),
            'pageNumber'    => $request->query('page'),
            'entity'        => $request->query('entity')
        ]))->execute();
    }

    /**
     * Get the kuda account details of a specified customer
     *
     * @urlParam id string required The primary key User ID of the customer
     * @response {
     * "data": {
     * "account": {
     * "accountNumber": "2504194986",
     * "email": "lubem@gmail.com",
     * "phoneNumber": "08026459222",
     * "lastName": "Tser",
     * "firstName": "Lubem",
     * "middleName": null,
     * "bussinessName": null,
     * "accountName": "(Slait)-Tser Lubem",
     * "trackingReference": "2500f5d1-d9ab-4cbd-8186-711e4937361c",
     * "creationDate": "2023-04-03T16:11:11.29",
     * "isDeleted": false
     * }
     * },
     * "success": true,
     * "message": "Request successful.",
     * "meta_data": {
     * "requestRef": "transave-202401031207-i3upbyszu",
     * "serviceType": "ADMIN_RETRIEVE_SINGLE_VIRTUAL_ACCOUNT"
     * }
     * }
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function getVirtualAccountDetails($id)
    {
        return (new GetVirtualAccount(['user_id' => $id]))->execute();
    }
}
