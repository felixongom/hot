<?php
namespace Hot;

class Number{
    //generating sequence of number
    public static function sequence(int $from, string $to, int|float $steps = 1): array{
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
    //formatting numbers with separator like commas and dots, etc
    public static function format($number, string $formatter = ","){
        $number = (string) $number;
        $number_array = explode('.', $number);
        $number = $number_array[0];
        $str_length = strlen($number);
        $after_point = array_key_exists('1', $number_array)?$number_array[1]:null;
        $counter = 0;
        $formatted = '';
        // 
        for ($i = 0; $i < $str_length; $i++) { 
            $counter ++;
            $single_num = $number[($str_length - $i)-1];
            // 
            if($counter%3==0 && $single_num-1){
                $formatted = "$formatter$single_num$formatted";
            }else{
                $formatted = "$single_num$formatted";
            }
        }
        $results = $after_point?$formatted.".$after_point":$formatted;
        return $results;
    }
    //formatting numbers with separator like commas and dots, etc
    public static function matrix(int $number, int $precision = 0){
        $result = null;
        if ($number <100) {
            return $number;
        }elseif($number < 1000000){
            $result = (string)round($number/1000, $precision)."K";
        }elseif ($number <1000000000) {
            $result = (string)round($number/1000000, $precision)."M";
        }elseif ($number <1000000000000) {
            $result = (string)round($number/1000000000, $precision)."B";
        }elseif ($number <1000000000000000) {
            $result = (string)round($number/1000000000000, $precision)."T";
        }elseif ($number <1000000000000000000) {
            $result = (string)round($number/1000000000000000, $precision)."Q";
        }
        return $result;
    }
    //chopping some part of the array or string.
    public static function chop(string|array $input, int $from, int $to){
        $input = is_string($input)?str_split($input):$input;
        $from = $from<1?1:$from;
        $to = $to>count($input)?count($input):$to;
        $result = [];
        for ($i = $from-1; $i<$to; $i++){
            $result  = [...$result, $input[$i]];
        }
        return is_array($result)?$result:join($result);
    }
    //trancating but not rounding off
    public static function trancate(float $number, int $precision = 0){
        $number = (string) $number;
        $number = explode('.', $number);
        $trancated = join(self::chop($number[1], 0, $precision));
        return (float)$precision==0?$number[0]:$number[0].".".$trancated;
    }
}