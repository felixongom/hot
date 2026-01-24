<?php
namespace Hot;
class Response{
    //json.
    public static function json($data, int $status_code = 200){
        if(is_array($data) || is_object($data)){
            http_response_code($status_code);
            return json_encode($data);
        }else{
            return $data;
        }
    }
    //send json.
    public static function send($data, int $status_code = 200){
        http_response_code($status_code);
        echo self::json($data);
        exit();
    }
}