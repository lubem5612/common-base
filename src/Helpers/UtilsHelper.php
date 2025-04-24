<?php

namespace Transave\CommonBase\Helpers;

trait UtilsHelper
{
    /**
     * Remove characters from a string
     * @param string $value original string
     * @param array $data character to be removed
     * @return void
     */
    public function removeStrings(string $value, array $data) : string
    {
        $data = [...$data, 'undefined', null];
        return str_replace($data, "", $value);
    }
}
