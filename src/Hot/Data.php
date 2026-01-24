<?php
namespace Hot;
class Data{
   
    //getting env values data
    public static function env(string $key){
        $env = parse_ini_file('.env');
        if(!$key) return $env;
        if(array_key_exists($key, $env)){
            return $env[$key];
        }else{
            return null;
        }
    }
    
}