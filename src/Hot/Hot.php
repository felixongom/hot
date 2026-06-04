<?php

namespace Hot;

class Hot{

    // setting flash messege
    public static function flash(string $key, $messege = null){
        if (!isset($_SESSION)) session_start();
        $flash_key = "flash_$key";
        if ($messege === null) {//setting flash
            if (!array_key_exists($flash_key, $_SESSION)) return;
            $return_value = $_SESSION[$flash_key];
            unset($_SESSION[$flash_key]); //remove the key from session variable
            return $return_value;
        } else {//setting flash
            $_SESSION[$flash_key] = $messege;
        }
        return;
    }
    // setting session
    public static function session(string $session_key, $messege = null){
        if (!isset($_SESSION)) {
            session_start();
        }
        //
        if ($messege === null) {//getting session
            if (!array_key_exists($session_key, $_SESSION)) return;
            return $_SESSION[$session_key];
        } else {//setting session
            $_SESSION[$session_key] = $messege;
        }
        return;
    }
    // setting session
    public static function deleteSession(string $session_key = ''): void{
        if (!isset($_SESSION)) session_start();
        //
        if(!$session_key){
            session_destroy();
        }elseif ($session_key && array_key_exists($session_key, $_SESSION)) { //remove it
            unset($_SESSION[$session_key]);
        } elseif ($session_key==='') { //remove all
            session_destroy();
        }
    }

   
    //random string.
    private static function randomize(string $characters, int $length):string{
        $result = "";

        for ($i=0; $i < $length; $i++) {
            $result .=$characters[random_int(0,strlen($characters))-1];
        }
        return $result;
    }

    //random string.
    public static function randomString(int $length = 8):string{
        $numbers = "1234567890";
        $upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $characters = $numbers.$upper.strtolower($upper);
        return self::randomize($characters, $length);
    }

    //random letters.
    public static function randomLetters(int $length = 8):string{
        $upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $characters = $upper.strtolower($upper);
        return self::randomize($characters, $length);
    }
    
    //random number.
    public static function randomNumber(int $length = 8):string{
        $numbers = "1234567890";
        return self::randomize($numbers, $length);
    }
}