<?php 

namespace App\Services;

use Illuminate\Support\Facades\Crypt;

class EncryptionService
{
    public static function encryptWithKey($data, $key)
    {
        return base64_encode(openssl_encrypt(
            json_encode($data),
            'aes-256-cbc',
            $key,
            0,
            substr($key, 0, 16) 
        ));
    }

    public static function decryptWithKey($encryptedData, $key)
    {
        return json_decode(openssl_decrypt(
            base64_decode($encryptedData),
            'aes-256-cbc',
            $key,
            0,
            substr($key, 0, 16) 
        ), true);
    }
}
