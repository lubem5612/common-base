<?php


namespace Transave\CommonBase\Helpers;


use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaystackApiHelper
{
    use ValidationHelper, ResponseHelper;
    private $request;
    private $validatedData;
    private $response;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            return $this
                ->validateRequest()
                ->generateReference()
                ->initiateTransfer()
                ->buildResponse();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function initiateTransfer()
    {
        $url = config('commonbase.paystack.base_url').$this->validatedData['url'];
        $this->response = Http::withHeaders([
            'Authorization' => 'Bearer '.config('commonbase.paystack.secret_key'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Cache-Control' => 'no-cache',
        ])->withoutVerifying()->post($url, $this->validatedData['data'])->json();

        return $this;
    }

    private function buildResponse()
    {
        if ($this->response['status']) {
            if (array_key_exists('data', $this->response)) {
                return $this->sendSuccess($this->response['data'], $this->response['message']);
            }
            return $this->sendSuccess(null, $this->response['message']);
        }
        return $this->sendError('error in paystack api call');
    }

    private function generateReference()
    {
        $this->validatedData['data']['reference'] = 'transave-'.Carbon::now()->format('YmdHi').'-'.strtolower(Str::random(9));
        return $this;
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'url' => 'required|string',
            'data' => 'nullable|array',
        ]);

        return $this;
    }
}