<?php


namespace Transave\CommonBase\Actions\Support;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Helpers\UploadHelper;
use Transave\CommonBase\Http\Models\Support;


class DeleteSupport extends Action
{
    private Support $support;
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
            ->deleteSupport();
    }

    private function deleteFileIfExist()
    {
        $this->support = Support::query()->find($this->validatedInput['support_id']);
        if ($this->support->file) {
            $this->uploader->deleteFile($this->support->file);
        }
        return $this;
    }

    private function deleteSupport()
    {
        $this->support->delete();
        return $this->sendSuccess(null, 'support deleted successfully');
    }

    private function validateRequest()
    {
        $this->validatedInput = $this->validate($this->request, [
            'support_id' => 'required|exists:supports,id'
        ]);
        return $this;
    }
}