<?php


class Strings {

    public static function addCero($num) {
        if(intval($num)<=9)
            return "0".$num;
        return $num;
    }

}