<?php


namespace Transave\CommonBase\FileSystem\AzureBlob;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileManager
{
    public static $FILE_SIZE = 0;
    public static $UPLOADED_PATH = "";
    public static $MIME_TYPE = "";
    public static $IS_UPLOADED = false;
    public static $MESSAGE = "";
    public static $ERROR = null;

    public static function UploadFile(UploadedFile $file, string $folder)
    {
        try{
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid().'.'.$extension;

            $path = $file->storePubliclyAs($folder, $filename, 'azure');
            if ($path) {
                if (env('AZURE_STORAGE_PREFIX')) {
                    $data = config('commonbase.azure.storage_url').env('AZURE_STORAGE_PREFIX').'/'.$path;
                }else {
                    $data = config('commonbase.azure.storage_url').$path;
                }
                self::$FILE_SIZE = $file->getSize();
                self::$MIME_TYPE = $extension;
                self::$UPLOADED_PATH = $data;
                self::$IS_UPLOADED = true;
                self::$MESSAGE = "upload successful";
            }
        }catch (\Exception $exception) {
            self::$MESSAGE = $exception->getMessage();
            self::$ERROR = $exception->getTrace();
        }
        return self::response();
    }

    public static function UploadOrReplaceFile(UploadedFile $file, string $folder, $model, string $column)
    {
        try{
            if($model->$column) {
                $deleteAction = self::DeleteFile($model->$column);
                if (!$deleteAction["success"]) {
                    self::$MESSAGE = "unable to delete existing file";
                    return self::response();
                }
            }

            self::UploadFile($file, $folder);
            self::$MESSAGE = $model->$column? "file replaced successfully" : "file upload successful";

        }catch (\Exception $exception) {
            self::$MESSAGE = $exception->getMessage();
            self::$ERROR = $exception->getTrace();
        }
        return self::response();
    }

    public static function DeleteFile($file_url)
    {
        try{
            if(strpos($file_url, 'windows.net')) {
                Storage::disk('azure')->delete(self::getFilePath($file_url));
                self::$IS_UPLOADED = true;
                return self::response();
            }else
                self::$MESSAGE = "file is not azure instance";

        }catch (\Exception $exception) {
            self::$IS_UPLOADED = false;
            self::$MESSAGE = $exception->getMessage();
            self::$ERROR = $exception->getTrace();
        }
        return self::response();
    }

    private static function getFilePath($file_url)
    {
        if (env('AZURE_STORAGE_PREFIX')) {
            return Str::after($file_url, env('AZURE_STORAGE_PREFIX').'/');
        }
        return Str::after($file_url, config('commonbase.azure.storage_url'));
    }

    private static function response()
    {
        return [
            "success"       => self::$IS_UPLOADED,
            "upload_url"    => self::$UPLOADED_PATH,
            "mime_type"     => self::$MIME_TYPE,
            "size"          => self::$FILE_SIZE,
            "message"       => self::$MESSAGE,
            "errors"        => self::$ERROR,
        ];
    }
}