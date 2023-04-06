<?php


namespace Raadaapartners\Raadaabase\SMS\Helpers;


trait ResponseTrait
{
    public $data = [];
    public $message = "";
    public $success = true;

    public function buildResponse()
    {
        return [
            "data"      => $this->data,
            "message"   => $this->message,
            "success"   => $this->success,
        ];
    }
}