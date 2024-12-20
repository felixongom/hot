<?php
namespace Hot;

class Time{
    //time ago
    public static function ago($datetime):string | bool{
        $timestamp = strtotime($datetime);
        $current_time = time();
        $diff = $current_time - $timestamp;
        // 
        if ($diff < 1) {
            return false;
        }elseif ($diff < 60) {
            $text = $diff === 1 ? 'second' : 'seconds';
            return $diff . " $text";
        } elseif ($diff < 3600) {
            $num = round($diff/60);
            $text = $num === 1 ? 'minute' : 'minutes';
            return $num . " $text";
        } elseif ($diff < 86400) {
            $num = round($diff/3600);
            $text = $num === 1 ? 'hour' : 'hours';
            return $num . " $text";
        } elseif ($diff < 2592000) { // Less than 30 days
            $num = round($diff/86400);
            $text = $num === 1 ? 'day' : 'days';
            return $num . " $text";
        } elseif ($diff < 31536000) { // Less than 1 year
            $num = round($diff/2592000);
            $text = $num === 1 ? 'month' : 'minutes';
            return $num . " $text";
        } else {
            $num = round($diff/31536000);
            $text = $num === 1 ? 'year' : 'years';
            return $num . " $text";
        }
    }
    // time left
    public static function left($datetime):string | bool {
        $timestamp = strtotime($datetime);
        $current_time = time();
        $diff = $timestamp - $current_time;
        // 
        if ($diff < 0) {
            return false;
        } elseif ($diff < 60) {
            return $diff===1?$diff . " second":$diff . " seconds";
        } elseif ($diff < 3600) {
            $num = round($diff/60);
            $text = $num === 1 ? 'minute' : 'minutes';
            return $num . " $text";
        } elseif ($diff < 86400) {
            $num = round($diff/3600);
            $text = $num === 1 ? 'hour' : 'hours';
            return $num . " $text";
        } elseif ($diff < 2592000) { // Less than 30 days
            $num = round($diff/86400);
            $text = $num === 1 ? 'day' : 'days';
            return $num . " $text";
        } elseif ($diff < 31536000) { // Less than 1 year
            $num = round($diff/86400);
            $text = $num === 1 ? 'month' : 'months';
            return $num . " $text";
        } else {
            $num = round($diff/31536000);
            $text = $num === 1 ? 'year' : 'years';
            return $num . " $text";
        }
    }
}