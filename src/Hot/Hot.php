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
    public static function deleteSession(string $session_key = null): void{
        if (!isset($_SESSION)) session_start();
        //
        if($session_key = null){
            session_destroy();
        }elseif ($session_key && array_key_exists($session_key, $_SESSION)) { //remove it
            unset($_SESSION[$session_key]);
        } elseif ($session_key === null) { //remove all
            session_destroy();
        }
    }
    // redirection the path
    public static function redirect(string $path):void{
        header("Location: $path");
        die();
    }
   
    //random string.
    public static function random(int $length = 8):string{
        $numbers = "1234567890";
        $upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $characters = $numbers.$upper.strtolower($upper);
        $result = "";

        for ($i=0; $i < $length; $i++) {
            $result .=$characters[random_int(0,strlen($characters))-1];
        }
        return $result;
    }
}