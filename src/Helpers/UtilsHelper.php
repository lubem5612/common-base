<?php

namespace Transave\CommonBase\Helpers;


use Transave\CommonBase\Actions\VFD\Transfer\BankList;


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

    public function getBankNameByCode(string $code) :? string
    {
        $bankName = "";
        if ($code) {
            $response = (new BankList())->execute();
            abort_unless($response['success'], 403, 'failed in fetching bank list');

            $banks = $response['data']['bank'];

            foreach($banks as $bank) {
                if ($bank['code'] == $code) {
                    $bankName = $bank['name'];
                }
            }
        }

        return $bankName;
    }
}
