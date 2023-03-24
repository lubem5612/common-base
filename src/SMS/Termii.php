<?php


namespace Raadaa\RaadaaBase\SMS;




use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Raadaa\RaadaaBase\Helpers\ResponseHelper;
use Raadaa\RaadaaBase\SMS\Helpers\PhoneHelper;

class Termii
{
    use ResponseHelper;

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
        $this->channel = config('raadaabase.termii.channel');
        $this->sender_name = config('raadaabase.termii.username');
        $this->api_key = config('raadaabase.termii.api_key');
        $this->message_type = config('raadaabase.termii.message_type');
    }

    public function sendSMS()
    {
        try {
            $url = "https://api.ng.termii.com/api/sms/send";
            $recipient = (new PhoneHelper($this->number))->handle();
            $data = [
                'to' => $recipient,
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