<?php


class Excel {

    private $number_col;

    public function __construct()
    {
        $number_col = 0;
    }

    //funcion recursiva creada para reporte Excel que evalua los numeros > 0
    // y asigna la letra desde la A....hasta la Z y AA, AB, AC.....AZ
    public function getExcelCol($num, $letra_temp = false) {
        $numero = $num % 26;
        $letra = chr(65 + $numero);
        $num2 = intval($num / 26);
        if(!$letra_temp)
            $GLOBALS['number_col'] = $GLOBALS['number_col'] +1;

        if ($num2 > 0) {
            return getExcelCol($num2 - 1) . $letra;
        } else {
            return $letra;
        }
    }

}
