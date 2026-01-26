<?php
namespace Hot;

class Files{
    // upload files
    public static function upload($files, array $options){
        if(!isset($files)) return false;
        // 
        $default_setup = ['min_size'=>0,'max_size'=>null];
        $options = [$options,...$default_setup];
        $returned_filename = [];
        $i = 0;
        $originalFilenames = $files['name'];
        $fileSizes = $files['size'];
        $fileTempName = $files['tmp_name'];
        $allowed_extension = array_map(function($extension){return strtolower($extension);
        },$options['allowed_extension']);
        // 
        if(is_string($files['name'])){
            //upload single options
            $define_options = [
                'files'=>$files, 
                'allowed_extension'=>$options['allowed_extension'], 
                'min_size'=>$options['min_size'], 
                'max_size'=>$options['max_size'], 
                'upload_path'=>$options['upload_path'],
                'new_name'=>$options['new_name'],

            ];

             // Create directory if it does not exist
        if (!is_dir($options['upload_path'])) {
            $path = array_key_exists('upload_path',$options)?$options['upload_path']:'public/uploads';
            mkdir($path, 0777, true);
        }
            return self:: uploadOne($define_options);
        }else{
            foreach($originalFilenames  as $originalFilename){
                $file_extenson = pathinfo($originalFilename, PATHINFO_EXTENSION);
                if(count($allowed_extension) && !in_array($file_extenson, $allowed_extension)) continue;
                // 
                $random_name = Hot::random(20);
                $new_name = "__$originalFilename __$random_name.$file_extenson";
                $fileSize = ($options['min_size'] || $options['max_size']) ?  $fileSizes[$i]:0;
                // 
                if($fileSize >= $fileSizes['min_size'] || $fileSize <= $fileSizes['max_size']) {
                    // move to upload folder
                    $upload_path = $options['upload_path'];
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
   
    private static function uploadOne(array $options){
        $default_setup = ['min_size'=>0,'max_size'=>null];
        $options = [$options,...$default_setup];
        // 
        $file_extenson = pathinfo($options['file']['name'], PATHINFO_EXTENSION);
        if(count($options['allowed_extension']) && !in_array($options['file_extenson'], $options['allowed_extension'])) return false;
        // 
        $random_name = Hot::random(20);

        $new_name = null;

        if(array_key_exists('fixed_name', $options)){
            $new_name = $options['fixed_name'].$file_extenson;
        }else{
            $new_name =(array_key_exists('new_name', $options)?("__".$options['new_name']."___$random_name"):("__".$options['file']['name']."___$random_name")).$file_extenson;
        }
        
        $fileSize = ($options['min_size'] || $options['max_size']) ?  $options['file']['size']:0;
        // 
        if($fileSize >= $options['min_size'] || $options['fileSize'] <= $options['max_size']) {
            // move to upload folder
            $upload_path = $options['upload_path'];
            move_uploaded_file($options['file']['tmp_name'], "$upload_path/$new_name");
            return ['name'=>$new_name, 'type'=>$options['file']['type']];
        }
        
    }
    // delete file
    public static function delete(array|string $file_name, string $file_dir){
        if(is_string($file_name)){
            $file = "$file_dir/$file_name";
            self::exist($file, $file_dir)? unlink($file):null;
            clearstatcache();
        }elseif(is_array($file_name)){
            foreach ($file_name as $path) {
                $file = "$file_dir/$path";
                self::exist($file, $file_dir)? unlink($file):null;
                clearstatcache();
                
            }
        }
    }
    // get file
    public static function getFiles(string|array $file_name, string $file_dir, string $default = ''){
        if(is_string($file_name)){
            $full_name = "$file_dir/$file_name";
            // 
            if(self::exist($full_name, $file_dir)){
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
                if(self::exist($full_name, $file_dir)){
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
    public static function exist(string $file_path, string $path_to_files):bool{
        $exists = file_exists($path_to_files.$file_path)?true:false;
        clearstatcache();
        return $exists;
    }
}