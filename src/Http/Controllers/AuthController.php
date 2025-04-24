<?php


namespace Transave\CommonBase\Http\Controllers;


use Illuminate\Http\Request;
use Transave\CommonBase\Actions\Auth\Login;
use Transave\CommonBase\Actions\Auth\ResendToken;
use Transave\CommonBase\Actions\Auth\VerifyAccount;
use Transave\CommonBase\Actions\VFD\Account\CreateVirtualAccount;
use Transave\CommonBase\Helpers\ResponseHelper;

/**
 * @group Authentication Controller Endpoints
 *
 * API routes for handling user authentication
 */
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
     * @bodyParam username string required The email or phone number of the user
     * @bodyParam password string required The password of the user
     * @response {
     * "success": true,
     * "data": {
     * "id": "c0329e2b-67d8-4c30-8bd9-51208c0b5975",
     * "first_name": "Emmanuel",
     * "last_name": "Maduka",
     * "middle_name": null,
     * "business_name": null,
     * "email": "emmanuel@raadaa.com",
     * "phone": "+2348026459222",
     * "account_number": "2504199022",
     * "withdrawal_limit": "50000.00000000",
     * "role": "customer",
     * "verification_token": null,
     * "is_verified": "yes",
     * "account_type": "ordinary",
     * "account_status": "verified",
     * "created_at": "2023-09-25T07:17:05.000000Z",
     * "updated_at": "2023-10-25T18:35:25.000000Z",
     * "deleted_at": null,
     * "token": "54|1Rp2Ricd0WuYLOsPfHrxjh0dChOWIX7ZZV5Xi8Rv",
     * "account": {
     * "percentage_completion": 0,
     * "missing_fields": [
     * "image_url",
     * "identity_card_url",
     * "address_proof_url",
     * "identity_type",
     * "identity_card_number",
     * "country_of_origin_id",
     * "country_of_residence_id",
     * "state_id",
     * "lga_id",
     * "city",
     * "next_of_kin",
     * "next_of_kin_contact",
     * "mother_maiden_name",
     * "residential_status",
     * "employment_status",
     * "employer",
     * "job_title",
     * "educational_qualification",
     * "income_range"
     * ],
     * "kyc": {
     * "id": "078140cb-3693-4d9b-902d-b58797a50d60",
     * "user_id": "c0329e2b-67d8-4c30-8bd9-51208c0b5975",
     * "image_url": null,
     * "identity_card_url": null,
     * "address_proof_url": null,
     * "identity_type": null,
     * "identity_card_number": null,
     * "country_of_origin_id": null,
     * "country_of_residence_id": null,
     * "state_id": null,
     * "lga_id": null,
     * "city": null,
     * "next_of_kin": null,
     * "next_of_kin_contact": null,
     * "mother_maiden_name": null,
     * "residential_status": null,
     * "employment_status": null,
     * "employer": null,
     * "job_title": null,
     * "educational_qualification": null,
     * "date_of_employment": null,
     * "number_of_children": null,
     * "income_range": null,
     * "verification_status": "unverified",
     * "is_loan_compliant": "no",
     * "created_at": "2023-09-25T07:17:05.000000Z",
     * "updated_at": "2023-09-25T07:17:05.000000Z"
     * }
     * },
     * "url": "/customer",
     * "wallet": {
     * "ledgerBalance": 4220600,
     * "availableBalance": 4220600,
     * "withdrawableBalance": 4220600
     * }
     * },
     * "message": "user logged in successfully."
     * }
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
        return (new ResendToken(['phone' => $request->get('phone')]))->execute();
    }

    /**
     * Verify registered account
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|VerifyAccount
     */
    public function verifyAccount(Request $request)
    {
        return (new VerifyAccount(['verification_token' => $request->get('verification_token')]))->execute();
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
