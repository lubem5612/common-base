<?php


namespace Raadaa\RaadaaBase\Helpers;


trait ResponseHelper
{
    public $data = null,
    public string $message = '';
    public boolean $success = false;

    public function buildResponse()
    {
        return [
            "success"   => $this->success,
            "data"      => $this->data,
            "message"   => $this->message,
        ];
    }
}