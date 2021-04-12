<?php


class Strings {

    public static function addCero($num) {
        if(intval($num)<=9)
            return "0".$num;
        return $num;
    }

    public static function rdecimal($number, $precision = 1, $separator = '.', $separatorDecimal = ',') {
        $numberParts = explode($separator, $number);
        $response = number_format(floatval($numberParts[0]), 0, ",", ".");
        if (count($numberParts) > 1) {
            $response .= $separatorDecimal;
            $response .= substr(
                $numberParts[1],
                0,
                $precision
            );
        }
        return $response;
    }

}