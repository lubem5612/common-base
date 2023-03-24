<?php


namespace Raadaapartners\RaadaaBase\Helpers;


trait ResponseHelper
{
    public $data = '';
    public $message = '';
    public $success = false;

    public function buildResponse()
    {
        return [
            "success"   => $this->success,
            "data"      => $this->data,
            "message"   => $this->message,
        ];
    }
}