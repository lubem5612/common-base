<?php


namespace Transave\CommonBase\Helpers;


use Illuminate\Support\Str;

trait PhoneHelper
{
    private function getInternationalNumber($phone)
    {
        if (Str::startsWith($phone, '0')) {
            return '+234'.ltrim($phone, '0');
        }else {
            if (Str::startsWith($phone, '234')) {
                return '+'.$phone;
            }else {
                $countPlus = substr_count($phone, '+234');
                $offset = $countPlus * 4;
                $phoneFormatted = substr($phone, $offset);
                return Str::start($phoneFormatted, '+234');
            }
        }
    }

    public function formatNumber($phone)
    {
        $intFormat = $this->getInternationalNumber($phone);
        return Str::after($intFormat, '+');
    }
}
