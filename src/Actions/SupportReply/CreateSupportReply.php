<?php


namespace Transave\CommonBase\Actions\SupportReply;


use Illuminate\Support\Arr;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Helpers\UploadHelper;
use Transave\CommonBase\Http\Models\SupportReply;

class CreateSupportReply extends Action
{
    private $request;
    private $validatedInput;
    private $uploader;

    public function __construct(array $request)
    {
        $this->request = $request;
        $this->uploader = new UploadHelper();
    }

    public function handle()
    {
        return $this
            ->validateRequest()
            ->setUploadFileUrl()
            ->createSupportReply();
    }

    private function setUploadFileUrl()
    {
        if (array_key_exists('file', $this->request)) {
            $response = $this->uploader->uploadFile($this->request['file'], 'support-replies');
            if ($response['success']) {
                $this->validatedInput['file'] = $response['upload_url'];
            }
        }
        return $this;
    }

    private function createSupportReply()
    {
        $supportReply = SupportReply::query()->create($this->validatedInput);
        return $this->sendSuccess($supportReply->load('support', 'user'), 'support reply created successfully');
    }

    private function validateRequest()
    {
        $data = $this->validate($this->request, [
            'user_id' => 'required|exists:users,id',
            'support_id' => 'required|exists:supports,id',
            'content' => 'nullable',
            'file' => 'nullable|file|max:5000|mimes:jped,jpg,gif,webp,pdf,doc,docx',
        ]);
        $this->validatedInput = Arr::except($data, ['file']);
        return $this;
    }
}