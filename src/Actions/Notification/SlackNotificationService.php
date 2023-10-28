<?php


namespace Transave\CommonBase\Actions\Notification;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Transave\CommonBase\Helpers\ValidationHelper;

class SlackNotificationService
{
    use ValidationHelper;
    private $request, $validatedData;
    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            $this->validateRequest();
            $this->processBlocks();
            $this->sendNotification();
        }catch (\Exception $exception)
        {
            Log::channel('slack')->error('slack notification error '. json_encode($exception->getTraceAsString()));
        }
    }

    private function processBlocks()
    {
        if (Arr::exists($this->validatedData, 'blocks') && is_array($this->validatedData['blocks']) && count($this->validatedData['blocks']) > 0) {
            $blocks = [];
            foreach ($this->validatedData['blocks'] as $index => $text)
            {
                $item['type'] = 'section';
                $item['text']['type'] = 'mrkdwn';
                $item['text']['text'] = $index.' : '.$text;

                array_push($blocks, $item);
            }

            $this->validatedData['blocks'] = $blocks;
        }
    }

    private function sendNotification()
    {
        $url = $this->validatedData['url'];
        $data = Arr::except($this->validatedData, ['url']);
        Http::post($url, $data);
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'url' => 'required|string',
            'message' => 'required|string',
            'blocks' => 'nullable|array',
        ]);
    }
}