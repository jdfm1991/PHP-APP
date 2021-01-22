<?php

class Funciones {

    public static function convertir($string, $abreviado = false){

        if (!$abreviado) {
            $string = str_replace(
                array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'),
                array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', ' DICIEMBRE'),
                $string
            );
        } else {
            $string = str_replace(
                array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'),
                array('ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', ' DIC'),
                $string
            );
        }

        return $string;
    }

    public static function addCero($num) {
        if(intval($num)<=9)
            return "0".$num;
        return $num;
    }

    public static function check_in_range($date_start, $date_end, $date_toevaluate) {
        $date_start = strtotime($date_start);
        $date_end = strtotime($date_end);
        $date_toevaluate = strtotime($date_toevaluate);
        if (($date_toevaluate >= $date_start) && ($date_toevaluate <= $date_end))
            return true;
        return false;
    }

}