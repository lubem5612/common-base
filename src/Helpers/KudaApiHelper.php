<?php


namespace Transave\CommonBase\Helpers;


use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KudaApiHelper
{
    use ValidationHelper, ResponseHelper;
    private ?string $reference;
    private ?string $accessToken;
    private ?array $validatedData;
    private ?array $request;
    private $kudaResponse = null;
    private $kudaStatus = false;
    private $kudaMessage = '';

    public function __construct(array $dataArray)
    {
        $this->request = $dataArray;
    }

    public function execute()
    {
        try {
            $this->validateRequest();
            $this->generateReference();
            $this->generateAccessToken();
            $this->makeApiCall();

        }catch (\Exception $e)
        {
            Log::error($e);
            $this->kudaMessage = $e->getMessage();
            $this->kudaStatus = false;
        }
        return [
            'data'          => $this->kudaResponse,
            'success'       => $this->kudaStatus,
            'message'       => $this->kudaMessage,
            'meta_data'     => [
                'requestRef' => $this->validatedData['requestRef'],
                'serviceType'=> $this->validatedData['serviceType']
            ]
        ];
    }

    /**
     * generate access token
     *
     * @return mixed
     */
    private function generateAccessToken()
    {
        $this->accessToken = Http::withHeaders([
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
    private function generateReference()
    {
        $this->validatedData['requestRef'] = 'transave-'.Carbon::now()->format('YmdHi').'-'.strtolower(Str::random(9));
    }

    /**
     * process Api response from Kuda
     *
     * @param $serviceType
     * @param array $data
     * @return array
     */
    private function makeApiCall()
    {
//        return $this->validatedData;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->accessToken,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Cache-Control' => 'no-cache',
        ])->withoutVerifying()->post(config('commonbase.kuda.base_url').'/', $this->validatedData)->json();

        if (!is_array($response)) {
            $this->kudaMessage = 'error in api response';
            $this->kudaResponse = $response;
        }elseif (array_key_exists('status', $response) && $response['status']) {
            $this->kudaResponse = isset($response['data'])? $response['data'] : null;
            $this->kudaMessage = isset($response['message'])? $response['message'] : 'api call successful';
            $this->kudaStatus = true;
        }else {
            $this->kudaMessage = array_key_exists('message', $response)?
                $response['message'] : 'unknown message content from api';
        }

    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'serviceType' => 'required|string',
            'data' => 'nullable|array'
        ]);
    }
}