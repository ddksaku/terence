<?php

namespace Models;

class Leonardo
{
    const DEFAULT_WORK_FACTOR = 8;

    public static function hash($password)
    {
        if(!function_exists('openssl_random_pseudo_bytes'))
            throw new Exception('Bcrypt requires openssl PHP extension');

        $salt = 
            '$2a$' . str_pad(self::DEFAULT_WORK_FACTOR, 2, '0', STR_PAD_LEFT) . '$' .
            substr(
                strtr(base64_encode(openssl_random_pseudo_bytes(16)), '+', '.'), 
                0, 22
            );

        // 
        
        return crypt($password, $salt);
    }
 
    public static function check($password, $stored_hash)
    {
        return crypt($password, $stored_hash) === $stored_hash;
    }
}