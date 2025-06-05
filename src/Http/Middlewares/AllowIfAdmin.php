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
        $user = auth('sanctum')->user();
        if (empty($user)) {
            return $this->sendError('you must be authenticated to proceed');
        }
        if (!in_array($user->role, ['admin', 'superadmin', 'support'])) {
            return $this->sendError('you must log in as admin to proceed');
        }

        return $next($request);
    }
}