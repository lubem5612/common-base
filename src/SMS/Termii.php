<?php


namespace Transave\CommonBase\SMS;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Transave\CommonBase\Helpers\ManagePhone;
use Transave\CommonBase\SMS\Helper\ResponseTrait;


class Termii
{
    use ResponseTrait, ManagePhone;

    private string $text;
    private string $sender_name;
    private string $number;
    private string $channel;
    private string $api_key;
    private string $message_type;

    public function __construct(string $number, string $message)
    {
        $this->text = $message;
        $this->number = $number;
        $this->channel = config('commonbase.termii.channel');
        $this->sender_name = config('commonbase.termii.username');
        $this->api_key = config('commonbase.termii.api_key');
        $this->message_type = config('commonbase.termii.message_type');
    }

    public function sendSMS()
    {
        try {
            $url = "https://api.ng.termii.com/api/sms/send";
            $data = [
                'to' => $this->formatNumber($this->number),
                'from' => $this->sender_name,
                'sms' => $this->text,
                'type' => $this->message_type,
                'channel' => $this->channel,
                'api_key' => $this->api_key,
            ];
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->api_key,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Cache-Control' => 'no-cache',
            ])->withoutVerifying()->post($url, $data)->json();

            if ( str_contains(strtolower($response['message']), 'success') ) {
                $this->message = "message sent successful";
                $this->data = $response;
                $this->success = true;
            }else {
                $this->data = $response;
                $this->message = 'failed in sending message';
            }
        }catch (\Exception $exception) {
            Log::error($exception);
            $this->message = $exception->getMessage();
        }
        return $this->buildResponse();
    }
}