<?php


namespace Transave\CommonBase\Helpers;


use Illuminate\Support\Str;

trait ManagePhone
{
    private function getInternationalNumber($phone)
    {
        if (Str::startsWith($phone, '0')) {
            return '+234'.$phone;
        }else {
            $countPlus =substr_count($phone, '+234');
            $offset = $countPlus * 4;
            $phoneFormatted = substr($phone, $offset);
            return Str::start($phoneFormatted, '+234');
        }
    }

    public function formatNumber($phone)
    {
        $intFormat = $this->getInternationalNumber($phone);
        return Str::after($intFormat, '+');
    }
}