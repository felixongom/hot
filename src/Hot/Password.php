<?php

namespace Hot;

class Password{
    //hash
    public static function hash(int|string $plain_password):string{
        return password_hash($plain_password, PASSWORD_DEFAULT);
    }
    //bcrypt
    public static function bcrypt(int|string $plain_password):string{
        return password_hash($plain_password, PASSWORD_BCRYPT);
    }
    //algon2i
    public static function algon2i(int|string $plain_password):string{
        return password_hash($plain_password, PASSWORD_ARGON2I);
    }
    //algon2i
    public static function algon21i(int|string $plain_password):string{
        return password_hash($plain_password, PASSWORD_ARGON2ID);
    }
    //verify
    public static function verify($plain_text, $hash_text){
        return password_verify($plain_text, $hash_text);
    }
}


