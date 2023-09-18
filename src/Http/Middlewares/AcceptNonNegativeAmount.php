<?php


namespace Transave\CommonBase\Http\Middlewares;
use Closure;
use Illuminate\Http\Request;
use Transave\CommonBase\Helpers\ResponseHelper;

class AcceptNonNegativeAmount
{
    use ResponseHelper;

    public function handle(Request $request, Closure $next)
    {
        $moneyKeys = config('commonbase.monetary_keys');
        foreach ($moneyKeys as $key) {
            if ($request->has($key) && (float)$request->get($key) < 0)
            {
                return $this->sendError('only non negative monetary values allowed');
            }
        }

        return $next($request);
    }
}