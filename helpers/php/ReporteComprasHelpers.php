<?php

class ReporteComprasHelpers
{
    public static function rentabilidad($precio1p = 0, $costoActual1p = 0)
    {
        $r1= $precio1p - $costoActual1p;
        $r = ($r1 == 0) ? 0 : $r1;
        $p1 = $r * 100;
        $p = ($p1 == 0) ? 0 : $p1;

        $porcentaje = ($p == 0 OR $precio1p == 0)
            ?  0
            :  $p / $precio1p;

        return $porcentaje;
    }

    public static function ventasMesAnterior($ventas = array(), $mes)
    {
        $output = array(
            'semana1' => 0,
            'semana2' => 0,
            'semana3' => 0,
            'semana4' => 0,
        );

        if (is_array($ventas) == true AND count($ventas) > 0 AND !empty($mes))
        {
            $M10 = $N10 = $O10 = $P10 = 0;

            foreach ($ventas as $venta)
            {
                $fechaEvaluar = Dates::normalize_date($venta['fechae']);

                $fechai_1 = date(FORMAT_DATE_TO_EVALUATE, mktime(0, 0, 0, ($mes) - 1, 1, date('Y')));
                $fechaf_7 = date(FORMAT_DATE_TO_EVALUATE, mktime(0, 0, 0, ($mes) - 1, 7, date('Y')));

                $fechai_8  = date(FORMAT_DATE_TO_EVALUATE, mktime(0, 0, 0, ($mes) - 1, 8,  date('Y')));
                $fechaf_14 = date(FORMAT_DATE_TO_EVALUATE, mktime(0, 0, 0, ($mes) - 1, 14, date('Y')));

                $fechai_15 = date(FORMAT_DATE_TO_EVALUATE, mktime(0, 0, 0, ($mes) - 1, 15, date('Y')));
                $fechaf_21 = date(FORMAT_DATE_TO_EVALUATE, mktime(0, 0, 0, ($mes) - 1, 21, date('Y')));

                switch(true) {
                    case Dates::check_in_range($fechai_1,  $fechaf_7,  $fechaEvaluar):
                        $M10 += $venta['cantidadBult']; break;
                    case Dates::check_in_range($fechai_8,  $fechaf_14, $fechaEvaluar):
                        $N10 += $venta['cantidadBult']; break;
                    case Dates::check_in_range($fechai_15, $fechaf_21, $fechaEvaluar):
                        $O10 += $venta['cantidadBult']; break;
                    default:
                        $P10 += $venta['cantidadBult'];
                }
            }

            $output = array(
                'semana1' => $M10,
                'semana2' => $N10,
                'semana3' => $O10,
                'semana4' => $P10,
            );
        }

        return $output;
    }
}