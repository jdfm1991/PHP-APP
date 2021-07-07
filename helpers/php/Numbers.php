<?php


class Numbers
{
    public static function avoidNull($number) : float{
        return ( ! is_null($number) ) ? $number : 0;
    }
}