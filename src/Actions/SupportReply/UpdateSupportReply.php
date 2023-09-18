<?php


namespace Transave\CommonBase\Actions\SupportReply;


use Illuminate\Support\Arr;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Helpers\UploadHelper;
use Transave\CommonBase\Http\Models\SupportReply;

class UpdateSupportReply extends Action
{
    private $request;
    private $validatedInput;
    private $uploader;
    private SupportReply $supportReply;

    public function __construct(array $request)
    {
        $this->request = $request;
        $this->uploader = new UploadHelper();
    }

    public function handle()
    {
        return $this
            ->validateRequest()
            ->setSupportReply()
            ->setUploadFileUrl()
            ->updateSupportReply();
    }

    private function setUploadFileUrl()
    {
        if (array_key_exists('file', $this->request)) {
            $response = $this->uploader->uploadOrReplaceFile($this->request['file'], 'support-replies', $this->supportReply, 'file');
            if ($response['success']) {
                $this->validatedInput['file'] = $response['upload_url'];
            }
        }
        return $this;
    }

    private function setSupportReply()
    {
        $this->supportReply = SupportReply::query()->find($this->validatedInput['support_reply_id']);
        return $this;
    }

    private function updateSupportReply()
    {
        $this->supportReply->fill($this->validatedInput)->save();
        return $this->sendSuccess($this->supportReply->refresh(), 'support reply updated successfully');
    }

    private function validateRequest()
    {
        $data = $this->validate($this->request, [
            'support_reply_id' => 'required|exists:support_replies,id',
            'user_id' => 'nullable|exists:users,id',
            'support_id' => 'nullable|exists:supports,id',
            'content' => 'nullable',
            'file' => 'nullable|file|max:5000|mimes:jped,jpg,gif,webp,pdf,doc,docx',
        ]);
        $this->validatedInput = Arr::except($data, ['file']);
        return $this;
    }
}