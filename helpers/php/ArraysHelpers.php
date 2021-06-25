<?php


class ArraysHelpers
{
    public static function validate(array $arr) {
        return (is_array($arr) == true and count($arr) > 0);
    }

    public static function validateWithPosAndParameter(array $arr, int $position, string $parameter) {
        if (is_array($arr) == true and count($arr) > 0)
            return $arr[$position][$parameter];
        return 0;
    }
}