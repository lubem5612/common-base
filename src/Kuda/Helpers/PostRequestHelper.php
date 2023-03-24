<?php


namespace Raadaapartners\Raadaabase\Kuda\Helpers;


use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait PostRequestHelper
{

    /**
     * @var null
     */
    private $data = null;

    /**
     * @var string
     */
    private $message = "";

    /**
     * @var bool
     */
    private $success = false;

    /**
     * generate access token
     *
     * @return mixed
     */
    protected function generateAccessToken()
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache',
        ])->withoutVerifying()->post(config('raadaabase.kuda.base_url').'/Account/GetToken',
            [
                'email' => config('raadaabase.kuda.email'),
                'apiKey' => config('raadaabase.kuda.api_key')
            ]
        )->json();
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
            ])->withoutVerifying()->post(config('raadaabase.kuda.base_url'), $inputs)->json();

            $this->processResponse($response);
        }catch (\Exception $exception) {
            Log::error($exception);
            $this->message = $exception->getMessage();
        }
        return [
            "success" => $this->success,
            "message" => $this->message,
            "data" => $this->data,
        ];
    }

    /**
     * process response from API
     *
     * @param $response
     */
    private function processResponse($response)
    {
        if (array_key_exists('Message', $response)) {
            $this->message = $response["Message"];
        }elseif(array_key_exists('message', $response)) {
            $this->message = $response["message"];
        }elseif (array_key_exists('Status', $response)) {
            if ((string)$response['Status'] = "true") {
                $this->success = true;
            }
        }elseif (array_key_exists('status', $response)) {
            if ((string)$response['status'] = "true") {
                $this->success = true;
            }
        }elseif (array_key_exists('Data', $response)) {
            $this->data = $response["Data"];
        }elseif (array_key_exists('data', $response)) {
            $this->data = $response["data"];
        }else {
            $this->message = 'unknown response';
            $this->data = $response;
        }
    }

    protected function generateUniqueId()
    {
        return '999'.rand(1000000, 9999999);
    }
}