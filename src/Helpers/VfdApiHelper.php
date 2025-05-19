<?php


namespace Transave\CommonBase\Helpers;


use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VfdApiHelper
{
    use ValidationHelper, ResponseHelper;
    private ?string $accessToken;
    private ?array $request;
    private $vfdResponse = null;
    private $vfdStatus = false;
    private $vfdMessage = '';
    private $applicationJson = 'application/json';
    private string $endpoint;
    private string $httpMethod;

    public function __construct(array $dataArray, string $endpoint, string $httpMethod)
    {
        $this->request = $dataArray;
        $this->endpoint = $endpoint;
        $this->httpMethod = $httpMethod;
    }

    public function execute()
    {
        try {
            $this->generateAccessToken();
            $this->makeApiCall();

            return [
                'data'          => $this->vfdResponse,
                'success'       => $this->vfdStatus,
                'message'       => $this->vfdMessage
            ];

        }catch (\Exception $e)
        {
            Log::error($e);
            $this->vfdMessage = $e->getMessage();
            $this->vfdStatus = false;

            return [
                'data'          => $this->vfdResponse,
                'success'       => $this->vfdStatus,
                'message'       => $this->vfdMessage
            ];
        }
    }

    /**
     * generate access token
     *
     * @return mixed
     */
    private function generateAccessToken()
    {
        $accessToken = Cache::get('vfd_access_token');
        if (!$accessToken) {
            $response = Http::withHeaders([
                'Accept' => $this->applicationJson,
                'Content-Type' => $this->applicationJson,
                'Cache-Control' => 'no-cache',
            ])->withoutVerifying()->post(config('commonbase.vfd.token_url'), [
                "consumerKey"       => config('commonbase.vfd.consumer_key'),
                "consumerSecret"    => config('commonbase.vfd.consumer_secret'),
                "validityTime"      => "-1"
            ])->json();
    
            if ($response && array_key_exists("data", $response) && isset($response["data"]["access_token"])) {
                $this->accessToken = $response["data"]["access_token"];
                Cache::put('vfd_access_token', $this->accessToken);
            } else {
                abort_if(!isset($response["data"]["access_token"]), 500, "unable to retrieve access token");
            }
        } else {
            $this->accessToken = $accessToken;
        }
    }

    /**
     * process Api response from Kuda
     *
     * @param $serviceType
     * @param array $data
     * @return void
     */
    private function makeApiCall()
    {
        $httpMethod = $this->httpMethod;
        $requestPayload = $httpMethod != 'get' ? $this->request : null;

        $response = Http::withHeaders([
            'AccessToken' => $this->accessToken,
            'Content-Type' => $this->applicationJson,
            'Accept' => $this->applicationJson,
            'Cache-Control' => 'no-cache',
        ])->withoutVerifying()->$httpMethod(config('commonbase.vfd.base_url').$this->endpoint, $requestPayload)->json();

        if (!is_array($response)) {
            $this->vfdMessage = 'error in api response';
            $this->vfdResponse = $response;
        } elseif (array_key_exists('status', $response) && $response['status']) {
            $this->vfdResponse = isset($response['data']) ? $response['data'] : null;
            $this->vfdMessage = isset($response['message']) ? $response['message'] : 'api call successful';
            $this->vfdStatus = true;
        } else {
            $this->vfdMessage = array_key_exists('message', $response)
                ? $response['message']
                : 'unknown message content from api';
        }
    }
}
