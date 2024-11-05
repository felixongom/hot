<?php

namespace Hot;

use stdClass;

class Hot{
    //generating sequence of number
    public static function numbers(int $from, string $to, int|float $steps = 1): array{
        $result_array = [$from];
        if ($from < $to) {
            for ($i = 0; $i <= $to; $i++) {
                $new_value = $result_array[count($result_array) - 1] + $steps;
                if ($new_value > $to) break;
                $result_array = [...$result_array, $new_value];
            }
        } elseif ($to < $from) {
            for ($i = $from; $i >= $to; $i--) {
                $new_value = $result_array[count($result_array) - 1] - $steps;
                if ($new_value < $to) break;
                $result_array = [...$result_array, $new_value];
            }
        }
        return $result_array;
    }
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
    //checking if there is some content
    public static function exist($data):bool{
        if ($data) return true;
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
    // upload files
    public static function upload($files, string $upload_path, array $allowed_extension = [], $min_size = 0, $max_size = null){
        if(!isset($files)) return false;

        $returned_filename = [];
        $i = 0;
        $originalFilenames = $files['name'];
        $fileType = $files['type'];
        $fileSizes = $files['size'];
        $fileTempName = $files['tmp_name'];
        $allowed_extension = array_map(function($extension){return strtolower($extension);
        },$allowed_extension);
        // 
        if(is_string($files['name'])){
            //upload single files
            return self:: uploadSingle($files, $allowed_extension, $min_size, $max_size, $upload_path);
        }else{
            foreach($originalFilenames  as $originalFilename){
                $file_extenson = pathinfo($originalFilename, PATHINFO_EXTENSION);
                if(count($allowed_extension) && !in_array($file_extenson, $allowed_extension)) continue;
                // 
                $random_name = self::random(20);
                $new_name = "$originalFilename __$random_name.$file_extenson";
                $fileSize = ($min_size || $max_size) ?  $fileSizes[$i]:0;
                // 
                if($fileSize >= $min_size || $fileSize <= $max_size) {
                    // move to upload folder
                    move_uploaded_file($fileTempName[$i], "$upload_path/$new_name");
                    $returned_filename = [...$returned_filename, ['name'=>$new_name, 'type'=>$fileType[$i]]];
                }else{
                    continue;
                } 
                $i++;
                }
                return $returned_filename;
        }
    }
    // 
    private static function uploadSingle($file, $allowed_extension, $min_size, $max_size, $upload_path){
        $file_extenson = pathinfo($file['name'], PATHINFO_EXTENSION);
        if(count($allowed_extension) && !in_array($file_extenson, $allowed_extension)) return false;
        // 
        $random_name = self::random(20);
        $new_name = $file['name']."__$random_name.$file_extenson";
        $fileSize = ($min_size || $max_size) ?  $file['size']:0;
        // 
        if($fileSize >= $min_size || $fileSize <= $max_size) {
            // move to upload folder
            move_uploaded_file($file['tmp_name'], "$upload_path/$new_name");
            return ['name'=>$new_name, 'type'=>$file['type']];
        }
        
    }
    // delete file
    public static function delete(string $file_dir, array|string $file_name){
        if(is_string($file_name)){
            $file = "$file_dir/$file_name";
            self::fileExist($file)? unlink($file):null;
            clearstatcache();
        }elseif(is_array($file_name)){
            foreach ($file_name as $path) {
                $file = "$file_dir/$path";
                self::fileExist($file)? unlink($file):null;
                clearstatcache();
            }
        }
    }
    // get file
    public static function files(string $file_dir, string|array $file_name, string $default = null):array|string|null{
        if(is_string($file_name)){
            $full_name = "$file_dir/$file_name";
            // 
            if(self::fileExist($full_name)){
                return $full_name;
            }else {
                if(!$default){
                    return null;
                }else{
                    return is_link($default)?$default:"$file_dir/$default";
                }
            }
        }elseif(is_array($file_name)){
            $full_names = [];
            foreach ($file_name as $name) {
                $full_name = "$file_dir/$name";
                if(self::fileExist($full_name)){
                    $full_names = [...$full_names, $full_name];
                }else {
                    if(!$default){
                        continue;
                    }else{
                        $def =is_link($default)?$default:"$file_dir/$default";
                        $full_names = [...$full_names, $def];
                    }
                }
            }
            return $full_names;
        }
    }
    // file exist
    public static function fileExist(string $file_path):bool{
        $is_deleted = file_exists($file_path)?true:false;
        clearstatcache();
        return $is_deleted;
    }
    //getting post data
    public static function post($post = []){
        return (object) $post;
    }
    //getting post data
    public static function get($get = []){
        return (object) $get;
    }
}
