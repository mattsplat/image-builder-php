<?php

namespace Matt\Vips;

class Str
{
    const ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public static function generateRandomString($length = 10)
    {
        $charactersLength = strlen(self::ALPHABET);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= self::ALPHABET[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function randomString($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

}