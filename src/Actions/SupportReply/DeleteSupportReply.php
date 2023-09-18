<?php


namespace Transave\CommonBase\Actions\SupportReply;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Helpers\UploadHelper;
use Transave\CommonBase\Http\Models\SupportReply;

class DeleteSupportReply extends Action
{
    private SupportReply $supportReply;
    private $request, $validatedInput, $uploader;

    public function __construct(array $request)
    {
        $this->request = $request;
        $this->uploader = new UploadHelper();
    }

    public function handle()
    {
        return $this
            ->validateRequest()
            ->deleteFileIfExist()
            ->deleteSupportReply();
    }

    private function deleteFileIfExist()
    {
        $this->supportReply = SupportReply::query()->find($this->validatedInput['support_reply_id']);
        if ($this->supportReply->file) {
            $this->uploader->deleteFile($this->supportReply->file);
        }
        return $this;
    }

    private function deleteSupportReply()
    {
        $this->supportReply->delete();
        return $this->sendSuccess(null, 'support reply deleted successfully');
    }

    private function validateRequest()
    {
        $this->validatedInput = $this->validate($this->request, [
            'support_reply_id' => 'required|exists:support_replies,id'
        ]);
        return $this;
    }
}