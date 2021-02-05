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

    public static function searchQuantityDocumentsByDates($array, $fieldSearch, $search, $format)
    {
        $indexI = $indexF = 0;
        $bandera = true;

        for ($i=0; $i<count($array)-1&&$bandera==true; $i++) {
            $indexI=$i;
            if (date_format(date_create($search), $format) == date_format(date_create($array[$i][$fieldSearch]), $format)) {
                for($j=$i+1; $j<count($array) && date_format(date_create($search), $format)==date_format(date_create($array[$j-1][$fieldSearch]), $format); $j++) {
                    $indexF=$j;
                }
                $bandera=false;
            }
        }

        return $indexF - $indexI;

    }

}