<?php


class KpiHelpers
{
    public static function activacionBultosPorMarcasKpi($ruta, $marcasKpi, $kpi, $fechai, $fechaf) {
        $temp = array();
        foreach ($marcasKpi as $i => $marca)
            $temp[$marca] = count($kpi->bultosActivadosPorMarca($ruta, $marca, $fechai, $fechaf));

        return $temp;
    }

    public static function frecuenciaVisita($frecuencia) {
        $frecu_ot = isset($frecuencia) ? $frecuencia : 2;

        switch ($frecu_ot) {
            case 1:
                $visita = "Mensual";
                break;
            case 2:
                $visita = "Quincenal";
                break;
            case 4:
                $visita = "Semanal";
                break;
            default:
                $visita = "Semanal";
        }

        return $visita;
    }

    public static function objetivoFacturasMasNotasMensual($clientes, $diasHabiles, $frecuencia) {
        $frecu = $diasHabiles / 5;
        $frecu_ot = isset($frecuencia) ? $frecuencia : 2;

        switch ($frecu_ot) {
            case 1:
                $frecu = $frecu * 0.25;
                break;
            case 2:
                $frecu = $frecu * 0.5;
                break;
            case 4:
                $frecu = $frecu * 1;
                break;
        }

        return ($frecu * $clientes);
    }
}