<?php


namespace Transave\CommonBase\Actions\Flutterwave\Transfer;


class InitiateCardTransaction
{

    function encrypt(string $encryptionKey, array $payload)
    {
        $encrypted = openssl_encrypt(json_encode($payload), 'DES-EDE3', $encryptionKey, OPENSSL_RAW_DATA);
        return base64_encode($encrypted);
    }
}