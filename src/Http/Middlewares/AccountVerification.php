<?php


namespace Transave\CommonBase\Http\Middlewares;
use Closure;
use Illuminate\Http\Request;
use Transave\CommonBase\Helpers\ResponseHelper;

class AccountVerification
{
    use ResponseHelper;
    public function handle(Request $request, Closure $next)
    {
        $user = auth('sanctum')->user();
        if (empty($user) || $user->is_verified !='yes' || $user->account_status != 'verified') {
            $message = "Your account is $user->account_status, kindly contact admin - support@transave.com.ng";
            return $this->sendError($message, [], 403);
        }

        return $next($request);
    }

}