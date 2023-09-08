<?php


namespace Transave\CommonBase\Actions\SMS;


use Illuminate\Support\Facades\Http;
use Transave\CommonBase\Helpers\PhoneHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;

class TermiiService
{
    use ValidationHelper, ResponseHelper, PhoneHelper;
    private $request, $validatedData, $response;
    private $channel, $senderName, $apiKey, $messageType, $baseUrl;

    public function __construct(array $request)
    {
        $this->request = $request;
        $this->channel = config('commonbase.termii.channel');
        $this->senderName = config('commonbase.termii.username');
        $this->apiKey = config('commonbase.termii.api_key');
        $this->messageType = config('commonbase.termii.message_type');
        $this->baseUrl = config('commonbase.termii.base_url');
    }

    public function execute()
    {
        try {
            return $this
                ->validateRequest()
                ->setData()
                ->makeApiCall()
                ->sendResponse();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function setData()
    {
        $this->validatedData['to'] = $this->formatNumber($this->validatedData['to']);
        $this->validatedData['from'] = $this->senderName;
        $this->validatedData['type'] = $this->messageType;
        $this->validatedData['channel'] = $this->channel;
        $this->validatedData['api_key'] = $this->apiKey;

        return $this;
    }

    private function makeApiCall()
    {
        if (!app()->environment(['testing', 'local'])) {
            $this->response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Cache-Control' => 'no-cache',
            ])->withoutVerifying()->post($this->baseUrl, $this->validatedData)->json();
        }
        return $this;
    }

    private function sendResponse()
    {
        if ( array_key_exists('message', $this->response) && str_contains(strtolower($this->response['message']), 'success') ) {
            return $this->sendSuccess($this->response, 'message sent successfully');
        }else {
            return $this->sendError('failed in sending message', $this->response);
        }
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "to" => "required|string|max:14|min:10",
            "sms" => "required|string",
        ]);

        return $this;
    }

}