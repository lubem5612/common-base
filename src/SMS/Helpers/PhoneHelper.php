<?php


namespace Raadaa\RaadaaBase\SMS\Helpers;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Raadaa\RaadaaBase\Helpers\ResponseHelper;

class PhoneHelper
{
    use ResponseHelper;

    private string $phone;
    private string $internationalFormat;

    public function __construct(string $phone)
    {
        $this->phone = $phone;
    }

    public function handle()
    {
        try {
            $this->getInternationalNumber()->formatNumber();
        }catch (\Exception $exception) {
            Log::error($exception);
            $this->message = $exception->getMessage();
        }
        return $this->buildResponse();
    }

    private function getInternationalNumber()
    {
        if (Str::startsWith($this->phone, '0')) {
            $this->internationalFormat = '+234'.$this->phone;
        }else {
            $countPlus =substr_count($this->phone, '+234');
            $offset = $countPlus * 4;
            $phone = substr($this->phone, $offset);
            $this->internationalFormat = Str::start($phone, '+234');
        }
        return $this;
    }

    private function formatNumber()
    {
        $this->data = Str::after($this->internationalFormat, '+');
        $this->success = true;
        $this->message = "successful";
        return $this;
    }
}