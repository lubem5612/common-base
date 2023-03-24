<?php


namespace Raadaapartners\Raadaabase\Helpers;


use Illuminate\Support\Facades\Log;

trait ResponseHelper
{
    protected $data = '';
    protected $message = '';
    protected $success = false;

    protected function buildResponse() : array
    {
        return [
            "success"   => $this->success,
            "data"      => $this->data,
            "message"   => $this->message,
        ];
    }

    protected function successResponse($message, $data=null) : array
    {
        return [
            "success"   => true,
            "data"      => $data,
            "message"   => $message
        ];
    }

    protected function errorResponse($message, $errors=[]) : array
    {
        $response["success"] = false;
        $response["message"] = $message;
        $response["data"] = null;
        if ($errors) {
            $response["errors"] = $errors;
        }
        return $response;
    }

    protected function serverErrorResponse(\Exception $exception)
    {
        Log::error($exception);
        return [
            "success" => false,
            "data" => null,
            "errors" => $exception->getTrace(),
            "message" => $exception->getMessage(),
        ];
    }
}