<?php
namespace Hot;
class Request{
    //getting request body from api
    public static function body(bool $as_array = true):array|object|null{
        $postdata = json_decode(file_get_contents("php://input", true));
        return ($as_array===true? (array) $postdata: (object) $postdata)??null;
    }
    //getting request params from api
    public static function params(bool $as_array = true):array|object|null{
        return ($as_array===true? (array)$_GET: (object) $_GET)??null;
    }
    //getting post data 
    public static function post(bool $as_array = true):array|object|null{
        return ($as_array===true?(array) $_POST:(object) $_POST)??null;
    }
    //getting get data
    public static function get(bool $as_array = true):array|object|null{
        return ($as_array===true?(array) $_GET:(object) $_GET)??null;
    }
    //getting files
    public static function files(){
        if(!isset($_FILES)) return false;
        return isset($_FILES['files'])?$_FILES['files']:null;
    }

}
