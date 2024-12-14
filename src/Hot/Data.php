<?php
namespace Hot;

class Data{
    public $post = null;
    public $get = null;
    
    //getting post data 
    public static function post(array $post = []){
        self::$post = (object) $post;
        return (object) $post;
    }
    //getting get data
    public static function get(array $get = []){
        self::$get = (object) $get;
        return (object) $get;
    }
    //getting env values data
    public static function env(string $key = null){
        $env = parse_ini_file('.env');
        if($key == null) return $env;
        if(array_key_exists($key, $env)){
            return $env[$key];
        }else{
            return null;
        }
    }
    //checking if there is some content
    public static function exist($data):bool{
        if ($data) return true;
        if ($data ==='') return false;
        if (!$data) return false;
    }
    //json.
    public static function json($data){
        if(is_array($data)){
        return json_encode($data);
        }else{
            return $data;
        }
    }
    //send json.
    public static function send($data){
        echo self::json($data);
        exit();
    }
    //array.
    public static function array($data){
        if(is_object($data)){
        return get_object_vars($data);
        }else{
            return $data;
        }
    }
    //object.
    public static function object($data){
        if(is_array($data)){
        return (object) $data;
        }else{
            return $data;
        }
    }
}