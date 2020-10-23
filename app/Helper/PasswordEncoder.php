<?php


namespace App\Helper;


class PasswordEncoder
{
    /**
     * @param $plainText
     * @return false|string|null
     */
    static public function encode($plainText)
    {
        return password_hash($plainText, PASSWORD_BCRYPT, ["cost" => 12]);
    }

    /**
     * @param $plainText
     * @param $hash
     * @return bool
     */
    static public function verify($plainText, $hash)
    {
        return password_verify($plainText, $hash);
    }
}