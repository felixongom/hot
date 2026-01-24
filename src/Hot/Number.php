<?php
namespace Hot;

use InvalidArgumentException;

class Number{
    
    protected static array $units = [
        0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four',
        5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen',
        14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen',
        17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen'
    ];

    protected static array $tens = [
        20 => 'twenty', 30 => 'thirty', 40 => 'forty',
        50 => 'fifty', 60 => 'sixty', 70 => 'seventy',
        80 => 'eighty', 90 => 'ninety'
    ];
    protected static array $scales = [
        100 => 'hundred',
        1000 => 'thousand',
        1000000 => 'million',
        1000000000 => 'billion',
        1000000000000 => 'trillion',
    ];

    protected static array $currencies = [
        'UGX' => ['name' => 'Ugandan shilling', 'plural' => 'Ugandan shillings'],
        'USD' => ['name' => 'US dollar', 'plural' => 'US dollars'],
        'SHS' => ['name' => 'shilling', 'plural' => 'shillings'],
    ];

    /* ================================
     * NUMBER → WORDS (supports decimals)
     * ================================ */
    public static function numberToWords(float|int $number): string
    {
        if ($number == 0) return 'zero';

        $integer = floor($number);
        $decimal = $number - $integer;

        $words = self::convertInteger($integer);

        if ($decimal > 0) {
            $words .= ' point';
            foreach (str_split(substr((string)$decimal, 2)) as $digit) {
                $words .= ' ' . self::$units[(int)$digit];
            }
        }

        return trim($words);
    }

    protected static function convertInteger(int $number): string
    {
        if ($number < 20) return self::$units[$number];

        if ($number < 100) {
            $tens = intval($number / 10) * 10;
            return self::$tens[$tens] .
                ($number % 10 ? ' ' . self::$units[$number % 10] : '');
        }

        foreach (array_reverse(self::$scales, true) as $value => $name) {
            if ($number >= $value) {
                return self::convertInteger(intval($number / $value)) .
                    " $name " .
                    ($number % $value ? self::convertInteger($number % $value) : '');
            }
        }

        return '';
    }



    /* ================================
     * CURRENCY
     * ================================ */
    public static function currency(float $amount, string $code): string
    {
        if (!isset(self::$currencies[$code])) {
            throw new InvalidArgumentException("Unsupported currency: $code");
        }

        $currency = self::$currencies[$code];
        $words = self::numberToWords($amount);

        $name = $amount == 1
            ? $currency['name']
            : $currency['plural'];

        return "$words $name";
    }
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
    public static function format($number, string $formatter = ","):string{
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
    public static function matrix(int $number, int $precision = 0):string{
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
    public static function trancate(float $number, int $precision = 0):bool{
        $number = (string) $number;
        $number = explode('.', $number);
        $trancated = join(self::chop($number[1], 0, $precision));
        return (float)$precision==0?$number[0]:$number[0].".".$trancated;
    }
}