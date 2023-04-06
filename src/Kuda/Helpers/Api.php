<?php


namespace Transave\CommonBase\Kuda\Helpers;


use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait Api
{
    /**
     * generate access token
     *
     * @return mixed
     */
    protected function generateAccessToken()
    {
        return Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache',
        ])->withoutVerifying()->post(config('commonbase.kuda.base_url').'/Account/GetToken', [
            "email" => config('commonbase.kuda.email'),
            "apiKey" => config('commonbase.kuda.api_key')
        ])->json();
    }

    /**
     * generate transaction reference
     *
     * @param int $length
     * @return string
     */
    protected function generateReference($length=15)
    {
        return 'transave-'.Carbon::now()->format('YmdHi').'-'.strtolower(Str::random($length-6));
    }

    /**
     * process Api response from Kuda
     *
     * @param $serviceType
     * @param array $data
     * @return array
     */
    protected function processKuda($serviceType, $data = [])
    {
        try {
            $inputs = [
                'serviceType' => $serviceType,
                'requestRef' => $this->generateReference(),
                'data' => $data,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->generateAccessToken(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Cache-Control' => 'no-cache',
            ])->withoutVerifying()->post(config('commonbase.kuda.base_url').'/', $inputs)->json();

            return [ 'data' => $response, 'errors' => null, 'message' => 'api call successful' ];
        }catch (\Exception $exception) {
            Log::error($exception);
            return [ 'data' => null, 'errors' => $exception->getTrace(), 'message' => $exception->getMessage() ];
        }
    }

    protected function generateUniqueId()
    {
        return '999'.rand(1000000, 9999999);
    }
}