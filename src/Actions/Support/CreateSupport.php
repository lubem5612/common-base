<?php


namespace Transave\CommonBase\Actions\Support;


use Illuminate\Support\Arr;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Helpers\UploadHelper;
use Transave\CommonBase\Http\Models\Support;


class CreateSupport extends Action
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
            ->setSupportType()
            ->setSupportStatus()
            ->createSupport();
    }

    private function setUploadFileUrl()
    {
        if (array_key_exists('file', $this->request)) {
            $response = $this->uploader->uploadFile($this->request['file'], 'support');
            if ($response['success']) {
                $this->validatedInput['file'] = $response['upload_url'];
            }
        }
        return $this;
    }

    private function setSupportType()
    {
        if (!array_key_exists('type', $this->validatedInput)) {
            $this->validatedInput['type'] = "OTHERS";
        }
        return $this;
    }

    private function setSupportStatus()
    {
        $this->validatedInput['status'] = "opened";
        return $this;
    }

    private function createSupport()
    {
        $support = Support::query()->create($this->validatedInput);
        return $this->sendSuccess($support, 'support created successfully');
    }

    private function validateRequest()
    {
        $data = $this->validate($this->request, [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable',
            'type' => 'nullable|in:ACCOUNT_UPGRADE,FAILED_TRANSACTION,AUTH_ISSUES,OTHERS',
            'file' => 'nullable|file|max:5000|mimes:jped,jpg,gif,webp,pdf,doc,docx',
        ]);
        $this->validatedInput = Arr::except($data, ['file']);
        return $this;
    }
}