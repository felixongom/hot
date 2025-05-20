<?php
namespace Hot;

class File{
    // upload files

   public static function upload(array $params){
        if(!isset($params['files'])) return false;
        // 
        $default_setup = ['min_size'=>0,'max_size'=>null];
        $params = [$params,...$default_setup];
        $returned_filename = [];
        $i = 0;
        $originalFilenames = $params['files']['name'];
        $fileSizes = $params['files']['size'];
        $fileTempName = $params['files']['tmp_name'];
        $allowed_extension = array_map(function($extension){return strtolower($extension);
        },$params['allowed_extension']);
        // 
        if(is_string($params['files']['name'])){
            //upload single params
            $define_params = [
                'files'=>$params['files'], 
                'allowed_extension'=>$params['allowed_extension'], 
                'min_size'=>$params['min_size'], 
                'max_size'=>$params['max_size'], 
                'upload_path'=>$params['upload_path'],
                'new_name'=>$params['new_name']
            ];

            return self:: uploadOne($define_params);
        }else{
            foreach($originalFilenames  as $originalFilename){
                $file_extenson = pathinfo($originalFilename, PATHINFO_EXTENSION);
                if(count($allowed_extension) && !in_array($file_extenson, $allowed_extension)) continue;
                // 
                $random_name = Hot::random(20);
                $new_name = "$originalFilename __$random_name.$file_extenson";
                $fileSize = ($params['min_size'] || $params['max_size']) ?  $fileSizes[$i]:0;
                // 
                if($fileSize >= $fileSizes['min_size'] || $fileSize <= $fileSizes['max_size']) {
                    // move to upload folder
                    $upload_path = $params['upload_path'];
                    move_uploaded_file($fileTempName[$i], "$upload_path/$new_name");
                    $returned_filename = [...$returned_filename, $new_name];
                }else{
                    continue;
                } 
                $i++;
                }
                return $returned_filename??false;
        }
    }
    // 
   
    private static function uploadOne(array $params){
        $default_setup = ['min_size'=>0,'max_size'=>null];
        $params = [$params,...$default_setup];
        // 
        $file_extenson = pathinfo($params['file']['name'], PATHINFO_EXTENSION);
        if(count($params['allowed_extension']) && !in_array($params['file_extenson'], $params['allowed_extension'])) return false;
        // 
        $random_name = Hot::random(20);
        $new_name = (array_key_exists('new_name', $params)?$params['new_name']."___$random_name":$params['file']['name']."___$random_name").$file_extenson;
        $fileSize = ($params['min_size'] || $params['max_size']) ?  $params['file']['size']:0;
        // 
        if($fileSize >= $params['min_size'] || $params['fileSize'] <= $params['max_size']) {
            // move to upload folder
            $upload_path = $params['upload_path'];
            move_uploaded_file($params['file']['tmp_name'], "$upload_path/$new_name");
            return ['name'=>$new_name, 'type'=>$params['file']['type']];
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
    public static function files(string|array $file_name, string $file_dir, string $default = ''){
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