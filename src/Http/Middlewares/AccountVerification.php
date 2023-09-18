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
        $user = $request->user();
        if (empty($user) || $user->is_verified!='yes' || in_array($user->account_status, ['banned', 'suspended']))
        {
            return $this->sendError('your account is not active');
        }

        return $next($request);
    }

}