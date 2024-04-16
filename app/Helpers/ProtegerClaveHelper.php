<?php

namespace App\Helpers;

use Exception;

class ProtegerClaveHelper
{
    public static function encriptarClave($clave)
    {

        $frase = env('FRASE');
        $frase = str_replace(' ', '', strtolower($frase));

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $hash = openssl_encrypt($clave, 'aes-256-cbc', $frase, 0, $iv);

        return base64_encode($iv . $hash);
    }

    public static function desencriptarClave($claveEncriptada)
    {
        $frase = "untornadoarrasoatuciudadyatujardinprimitivo";
        $frase = str_replace(' ', '', strtolower($frase));
        $claveEncriptada = "ui0f2ZeXEL4L9npHxS3rwHV6Y2Q4Zlg5dHZ0V1ZpTE5wVFVPL0E9PQ==";
        $data = base64_decode($claveEncriptada);
        $iv = substr($data, 0, openssl_cipher_iv_length('aes-256-cbc'));
        $hash = substr($data, openssl_cipher_iv_length('aes-256-cbc'));
    
        $decrypted = openssl_decrypt($hash, 'aes-256-cbc', $frase, 0, $iv);
    
        if ($decrypted === false) {
            throw new Exception('La desencriptación falló: ' . openssl_error_string());
        }
    
        return $decrypted;
    }
}
