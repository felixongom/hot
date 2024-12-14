<?php
namespace Hot;

class File{
    // upload files
   public static function upload($files, string $upload_path, array $allowed_extension = [], $min_size = 0, $max_size = null){
        if(!isset($files)) return false;
        // 
        $returned_filename = [];
        $i = 0;
        $originalFilenames = $files['name'];
        $fileSizes = $files['size'];
        $fileTempName = $files['tmp_name'];
        $allowed_extension = array_map(function($extension){return strtolower($extension);
        },$allowed_extension);
        // 
        if(is_string($files['name'])){
            //upload single files
            return self:: uploadOne($files, $allowed_extension, $min_size, $max_size, $upload_path);
        }else{
            foreach($originalFilenames  as $originalFilename){
                $file_extenson = pathinfo($originalFilename, PATHINFO_EXTENSION);
                if(count($allowed_extension) && !in_array($file_extenson, $allowed_extension)) continue;
                // 
                $random_name = Hot::random(20);
                $new_name = "$originalFilename __$random_name.$file_extenson";
                $fileSize = ($min_size || $max_size) ?  $fileSizes[$i]:0;
                // 
                if($fileSize >= $min_size || $fileSize <= $max_size) {
                    // move to upload folder
                    move_uploaded_file($fileTempName[$i], "$upload_path/$new_name");
                    $returned_filename = [...$returned_filename, $new_name];
                }else{
                    continue;
                } 
                $i++;
                }
                return $returned_filename;
        }
    }
    // 
    private static function uploadOne($file, $allowed_extension, $min_size, $max_size, $upload_path){
        $file_extenson = pathinfo($file['name'], PATHINFO_EXTENSION);
        if(count($allowed_extension) && !in_array($file_extenson, $allowed_extension)) return false;
        // 
        $random_name = Hot::random(20);
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
    public static function delete(array|string $file_name, string $file_dir){
        if(is_string($file_name)){
            $file = "$file_dir/$file_name";
            self::exist($file)? unlink($file):null;
            clearstatcache();
        }elseif(is_array($file_name)){
            foreach ($file_name as $path) {
                $file = "$file_dir/$path";
                self::exist($file)? unlink($file):null;
                clearstatcache();
            }
        }
    }
    // get file
    public static function files(string|array $file_name, string $file_dir, string $default = null):array|string|null{
        if(is_string($file_name)){
            $full_name = "$file_dir/$file_name";
            // 
            if(self::exist($full_name)){
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
                if(self::exist($full_name)){
                    $full_names = [...$full_names, $full_name];
                }else {
                    if(!$default){
                        continue;
                    }else{
                        $def = is_link($default)?$default:"$file_dir/$default";
                        $full_names = [...$full_names, $def];
                    }
                }
            }
            return $full_names;
        }
    }
    // file exist
    public static function exist(string $file_path):bool{
        $is_deleted = file_exists($file_path)?true:false;
        clearstatcache();
        return $is_deleted;
    }
}