<?php
namespace core\validation;

class Validator
{
    public static function string($str, int $min = 1, int $max = INF): bool
    {
        $value = trim($str);
        return mb_strlen($value) >= $min && mb_strlen($value) <= $max;
    }

    public static function number($number, $min = 1, $max = INF): bool
    {
        return is_numeric($number) && $number >= $min && $number <= $max;
    }

    public static function phone($str): bool
    {
        return preg_match('/^\d{10}$/', $str) === 1;
    }


    public static function email($str): bool
    {
        return filter_var($str, FILTER_VALIDATE_EMAIL);
    }
}