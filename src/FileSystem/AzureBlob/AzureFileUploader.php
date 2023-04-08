<?php


namespace Transave\CommonBase\FileSystem\AzureBlob;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AzureFileUploader
{
    private UploadedFile $file;
    private Model $model;
    private string $folder;
    private string $model_column;
    private string $full_url;
    private string $relative_path;

    public function __construct(UploadedFile $file=null, string $folder=null, Model $model=null, string $model_column=null)
    {
        $this->file         = $file;
        $this->folder       = $folder;
        $this->model        = $model;
        $this->model_column = $model_column;
        $this->setRelativeUrlFromModel();
    }

    public function upload()
    {
        try{
            $extension = $this->file->getClientOriginalExtension();
            $filename = uniqid().'.'.$extension;

            $path = $this->file->storePubliclyAs($this->folder, $filename, 'azure');
            if ($path) {
                if (env('AZURE_STORAGE_PREFIX')) {
                    $data = config('app.storage_url').env('AZURE_STORAGE_PREFIX').'/'.$path;
                }else {
                    $data = config('app.storage_url').$path;
                }
                return $this->buildResponse('upload successful', true, $data);
            }
            return $this->buildResponse('upload failed');
        }catch (\Exception $exception) {
            Log::error($exception);
            $this->buildResponse($exception->getMessage(), false, $exception->getTrace());
        }
    }

    public function replaceOrUpload()
    {
        try{
            if (is_null($this->model) || empty($this->model)) {
                return $this->upload();
            }
            $column = $this->model_column;
            if ($this->model->$column) {
                $callback = $this->delete();
                if (!$callback['success'])
                    return $this->buildResponse($callback["message"].'. unable to delete existing file', $callback["data"]);
            }
            return $this->upload();
        }catch (\Exception $exception) {
            Log::error($exception);
            return $this->buildResponse($exception->getMessage(), false, $exception->getTrace());
        }
    }

    public function delete()
    {
        try{
            if (!$this->full_url || is_null($this->full_url)) {
                return $this->buildResponse('file path not found');
            }
            if(strpos($this->full_url, 'windows.net')) {
                $this->setFileRelativePath();
                Storage::disk('azure')->delete($this->relative_path);
                return $this->buildResponse( "file delete successful", true);
            }else {
                return $this->buildResponse("file is not azure storage instance");
            }
        }catch (\Exception $exception) {
           Log::error($exception);
           return $this->buildResponse($exception->getMessage(), false, $exception->getTrace());
        }
    }

    private function setFileRelativePath() : self
    {
        if (env('AZURE_STORAGE_PREFIX')) {
            $this->relative_path = Str::after($this->full_url, env('AZURE_STORAGE_PREFIX').'/');
        }else {
            $this->relative_path = Str::after($this->full_url, config('app.storage_url'));
        }
        return $this;
    }

    private function buildResponse($message, $success=false, $data=null) : array
    {
        return [
            "success" => $success,
            "data" => $data,
            "message" => $message
        ];
    }

    private function setRelativeUrlFromModel()
    {
        $file_path = $this->model_column;

        if (is_null($this->model) || empty($this->model)) {
            $this->full_url = null;
        }elseif (property_exists($this->model, $this->model_column)) {
            $this->full_url = $this->model->$file_path;
        }elseif (isset($this->model)){
            $this->full_url = $this->model->$file_path;
        }else {
            $this->full_url = null;
        }
    }


}