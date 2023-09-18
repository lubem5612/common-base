<?php


namespace Transave\CommonBase\Http\Middlewares;
use Closure;
use Transave\CommonBase\Helpers\ResponseHelper;
use Illuminate\Http\Request;

class AllowIfAdmin
{
    use ResponseHelper;
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (empty($user) || $user->role!='admin') {
            return $this->sendError('you must log in as admin to proceed');
        }

        return $next($request);
    }
}