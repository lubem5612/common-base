<?php


namespace Raadaa\RaadaaBase\SMS;




use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Raadaa\RaadaaBase\Helpers\ResponseHelper;
use Raadaa\RaadaaBase\SMS\Helpers\PhoneHelper;

class SmsService
{
    use ResponseHelper;

    private string $message;
    private string $sender_name;
    private array $numbers, $formattedNumbers;
    private string $route;

    public function __construct(array $numbers, string $message)
    {
        $this->message = $message;
        $this->numbers = $numbers;
        $this->route = config('raadaabase.sendchamp.route');
        $this->sender_name = config('raadaabase.sendchamp.username');
    }

    public function sendSMS()
    {
        try {
            $this->processNumbers();
            $data = [
                'to'            => $this->formattedNumbers,
                'message'       => $this->message,
                'sender_name'   => $this->sender_name,
                'route'         => $this->route,
            ];
            $url = 'https://api.sendchamp.com/api/v1/sms/send';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.config('raadaabase.sendchamp.public_key'),
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
            $format = (new PhoneHelper($number))->handle();
            if ($format['success'])
                array_push($this->formattedNumbers, $format['data']);
        }
    }
}