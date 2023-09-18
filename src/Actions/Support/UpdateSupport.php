<?php


namespace Transave\CommonBase\Actions\Support;


use Illuminate\Support\Arr;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Helpers\UploadHelper;
use Transave\CommonBase\Http\Models\Support;


class UpdateSupport extends Action
{
    private $request;
    private $validatedInput;
    private $uploader;
    private $support;

    public function __construct(array $request)
    {
        $this->request = $request;
        $this->uploader = new UploadHelper();
    }

    public function handle()
    {
        return $this
            ->validateRequest()
            ->setSupport()
            ->setUploadFileUrl()
            ->setSupportType()
            ->setSupportStatus()
            ->updateSupport();
    }

    private function setUploadFileUrl()
    {
        if (array_key_exists('file', $this->request)) {
            $response = $this->uploader->uploadOrReplaceFile($this->request['file'], 'support', $this->support, 'file');
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
        if (!array_key_exists('status', $this->validatedInput)) {
            $this->validatedInput['status'] = "opened";
        }
        return $this;
    }

    private function setSupport()
    {
        $this->support = Support::query()->find($this->validatedInput['support_id']);
        return $this;
    }

    private function updateSupport()
    {
        $this->support->fill($this->validatedInput)->save();
        return $this->sendSuccess($this->support->refresh(), 'support updated successfully');
    }

    private function validateRequest()
    {
        $data = $this->validate($this->request, [
            'support_id' => 'required|exists:supports,id',
            'user_id' => 'nullable|exists:users,id',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable',
            'type' => 'nullable|in:ACCOUNT_UPGRADE,FAILED_TRANSACTION,AUTH_ISSUES,OTHERS',
            'status' => 'nullable|in:closed,opened,archived',
            'file' => 'nullable|file|max:5000|mimes:jped,jpg,gif,webp,pdf,doc,docx',
        ]);
        $this->validatedInput = Arr::except($data, ['file']);
        return $this;
    }
}