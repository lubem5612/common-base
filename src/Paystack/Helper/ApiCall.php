<?php


namespace Transave\CommonBase\Paystack\Helper;


use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait ApiCall
{
    public function processPaystack(string $url, array $data)
    {
        $data["reference"] = 'transave-'.Carbon::now()->format('YmdHi').'-'.strtolower(Str::random(9));
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.config('commonbase.paystack.secret_key'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Cache-Control' => 'no-cache',
            ])->withoutVerifying()->post($url, $data)->json();

            return [ 'data' => $response, 'errors' => null, 'message' => 'api call successful' ];
        }catch (\Exception $exception) {
            Log::error($exception);
            return [ 'data' => null, 'errors' => $exception->getTrace(), 'message' => $exception->getMessage() ];
        }
    }
}