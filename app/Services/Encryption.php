<?php

namespace App\Services;

class Encryption
{
    static $ciphering = "AES-128-CTR";
    static $encryption_iv = "1234567891011121";
    static $key = "TheBestKey123";

    public static function encryption($string)
    {
        $ciphering = self::$ciphering;
  
        // Use OpenSSl Encryption method
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;
        
        // Non-NULL Initialization Vector for encryption
        $encryption_iv = self::$encryption_iv;
        
        // Store the encryption key
        $encryption_key = self::$key;
        
        // Use openssl_encrypt() function to encrypt the data
        $encryption = openssl_encrypt($string, $ciphering,
                    $encryption_key, $options, $encryption_iv);

        //убираем символ /
        $encryption = str_replace("/", "&", $encryption);
        return $encryption;
    }

    public static function decryption($string)
    {
        //Возвращаем  / вместо &
        $string = str_replace("&", "/", $string);
        $ciphering = self::$ciphering;
  
        // Use OpenSSl Encryption method
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;
        
        // Non-NULL Initialization Vector for encryption
        $encryption_iv = self::$encryption_iv;
        
        // Store the encryption key
        $encryption_key = self::$key;

        $decryption = openssl_decrypt($string, $ciphering, 
        $encryption_key, $options, $encryption_iv);

        return $decryption;
    }

}