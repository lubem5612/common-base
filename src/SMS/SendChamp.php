<?php


namespace Transave\CommonBase\SMS;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Transave\CommonBase\Helpers\ManagePhone;
use Transave\CommonBase\SMS\Helper\ResponseTrait;


class SendChamp
{
    use ResponseTrait, ManagePhone;

    private string $text;
    private string $sender_name;
    private array $numbers, $formattedNumbers;
    private string $route;
    public string $public_key;

    public function __construct(array $numbers, string $message)
    {
        $this->text = $message;
        $this->numbers = $numbers;
        $this->route = config('commonbase.sendchamp.route');
        $this->sender_name = config('commonbase.sendchamp.username');
        $this->public_key = config('commonbase.sendchamp.public_key');
    }

    public function sendSMS()
    {
        try {
            $this->processNumbers();
            $data = [
                'to'            => $this->formattedNumbers,
                'message'       => $this->text,
                'sender_name'   => $this->sender_name,
                'route'         => $this->route,
            ];
            $url = 'https://api.sendchamp.com/api/v1/sms/send';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->public_key,
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache',
            ])->withoutVerifying()->post($url, $data)->json();
            if ($response['code'] == 200) {
                $this->message = $response['message'];
                $this->data = $response['data'];
                $this->success = true;
            }
        }catch (\Exception $exception) {
            Log::error($exception);
            $this->message = $exception->getMessage();
        }
        return $this->buildResponse();
    }

    private function processNumbers()
    {
        foreach ($this->numbers as $number) {
            $format = $this->formatNumber($number);
            array_push($this->formattedNumbers, $format);
        }
    }
}