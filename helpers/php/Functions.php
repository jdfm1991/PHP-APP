<?php


class Functions {

    public static function searchQuantityDocumentsByDates($array, $fieldSearch, $search, $format)
    {
        $indexI = $indexF = 0;
        $flag = true;

        for ($i=0; $i<count($array)-1&&$flag==true; $i++) {
        $indexI=$i;
        if (date_format(date_create($search), $format) == date_format(date_create($array[$i][$fieldSearch]), $format)) {
        for($j=$i+1; $j<count($array) && date_format(date_create($search), $format)==date_format(date_create($array[$j-1][$fieldSearch]), $format); $j++) {
        $indexF=$j;
        }
        $flag=false;
        }
        }
        return $indexF - $indexI;
    }

}