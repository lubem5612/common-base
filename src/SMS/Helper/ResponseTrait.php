<?php


namespace Transave\CommonBase\SMS\Helper;


trait ResponseTrait
{
    protected $data = [];
    protected $message = "";
    protected $success = true;

    protected function buildResponse()
    {
        return [
            "data"      => $this->data,
            "message"   => $this->message,
            "success"   => $this->success,
        ];
    }
}