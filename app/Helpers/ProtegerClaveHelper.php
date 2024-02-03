<?php

namespace App\Helpers;

use Illuminate\Http\Response;

class ProtegerClaveHelper
{

    static function encriptarClave($clave)
    {

        $frase = env('FRASE');
        $frase = str_replace(' ', '', strtolower($frase));

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $hash = openssl_encrypt($clave, 'aes-256-cbc', $frase, 0, $iv);
        return base64_encode($iv . $hash);
    }

    static function desencriptarClave($claveEncriptada)
    {
        $frase = env('FRASE');
        $frase =str_replace(' ', '', strtolower($frase));

        $data = base64_decode($claveEncriptada);
        $iv = substr($data, 0, openssl_cipher_iv_length('aes-256-cbc'));
        $hash = substr($data, openssl_cipher_iv_length('aes-256-cbc'));
        return openssl_decrypt($hash, 'aes-256-cbc', $frase, 0, $iv);
    }
}
