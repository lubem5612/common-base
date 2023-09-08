<?php


namespace Transave\CommonBase\Helpers;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadHelper
{
    private $uploadedFileSize = 0;
    private $uploadedFilePath = '';
    private $uploadedFileExtension = '';
    private $disk = '';
    private $storageConfig = [];
    private $fileRealPath = '';
    private $isSuccessful = false;
    private $uploadedFileError = [];
    private $uploadedFileMessage = '';

    public function uploadFile(UploadedFile $uploadedFile, $folder)
    {
        try{
            $this->setStorageConfig();
            $extension = $uploadedFile->getClientOriginalExtension();
            $filename = uniqid().'.'.$extension;

            $path = $uploadedFile->storePubliclyAs($folder, $filename, $this->disk);
            if ($path) {
                $this->uploadedFilePath = $this->storageConfig['storage_url'].'/'.config('commonbase.storage.prefix').'/'.$path;
                $this->uploadedFileSize = $uploadedFile->getSize();
                $this->uploadedFileExtension = $extension;
                $this->isSuccessful = true;
                $this->uploadedFileMessage = "upload successful";
            }
        }catch (\Exception $exception) {
            $this->uploadedFileMessage= $exception->getMessage();
            $this->uploadedFileError = $exception->getTrace();
        }
        return $this->response();
    }

    public function deleteFile($file_url)
    {
        try {
            $this->setStorageConfig();
            $this->setRealPath($file_url);
            Storage::disk($this->disk)->delete($this->fileRealPath);
            $this->isSuccessful = true;
            $this->uploadedFileMessage = "deleted successfully";
        }catch (\Exception $exception) {
            $this->uploadedFileMessage= $exception->getMessage();
            $this->uploadedFileError = $exception->getTrace();
        }
        return $this->response();
    }

    public function uploadOrReplaceFile(UploadedFile $uploadedFile, $folder, $model, $column)
    {
        $this->setStorageConfig();
        try{
            if($model->$column) {
                $deleteAction = $this->deleteFile($model->$column);
                if (!$deleteAction["success"]) {
                    $this->uploadedFileMessage = "unable to delete existing file";
                    $this->isSuccessful = false;
                    return $this->response();
                }
            }

            $uploadAction = $this->uploadFile($uploadedFile, $folder);
            if (!$uploadAction['success']) {
                $this->uploadedFileMessage = "unable to upload new file";
                $this->isSuccessful = false;
                return $this->response();
            }
            $this->uploadedFileMessage = $model->$column? "file replaced successfully" : "file upload successful";

        }catch (\Exception $exception) {
            $this->uploadedFileMessage= $exception->getMessage();
            $this->uploadedFileError = $exception->getTrace();
        }
        return $this->response();
    }

    private function setRealPath($url)
    {
        $prefix = config('commonbase.storage.prefix').'/';
        $this->fileRealPath = Str::after($url, $prefix);
    }

    private function setStorageConfig()
    {
        $this->disk = config('commonbase.storage.driver');
        $this->storageConfig = config("commonbase.$this->disk");
    }

    private function response()
    {
        return [
            "success"       => $this->isSuccessful,
            "upload_url"    => $this->uploadedFilePath,
            "mime_type"     => $this->uploadedFileExtension,
            "size"          => $this->uploadedFileSize,
            "message"       => $this->uploadedFileMessage,
            "errors"        => $this->uploadedFileError,
        ];
    }
}