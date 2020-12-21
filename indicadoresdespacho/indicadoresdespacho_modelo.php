
<?php
//LLAMAMOS A LA CONEXION.
require_once("../acceso/conexion.php");

class InidicadoresDespachos extends Conectar{

    public function get_entregasefectivas_por_chofer($fechai, $fechaf, $id_chofer)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $sql= "SELECT det.correl AS correlativo, fecha_entre, tipo_pago, count(det.correl) AS cant_documentos,
               COALESCE(STUFF((SELECT ',' + a.numeros FROM appfacturas_det AS a WHERE a.correl=det.correl AND fecha_entre IS NULL FOR XML PATH ('')), 1, 2, ''), '') AS fact_sin_liquidar
               FROM appfacturas_det AS det WHERE correl IN ( SELECT correl AS correlativo FROM appfacturas WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fechad)) BETWEEN ? AND ? AND cedula_chofer = ?)
               GROUP BY fecha_entre, tipo_pago, correl ORDER BY fecha_entre";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$fechai);
        $sql->bindValue(2,$fechaf);
        $sql->bindValue(3,$id_chofer);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_causasrechazo_por_chofer($fechai, $fechaf, $id_chofer, $crechaz)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $rechaz = ($crechaz != "todos") ? " observacion= ? AND " : "";

        $chof = ($id_chofer != "-") ? " AND cedula_chofer = ?" : "";


        $sql= "SELECT det.correl AS correlativo, fecha_entre, count(det.correl) AS cant_documentos, tipo_pago, observacion
                FROM appfacturas_det AS det WHERE $rechaz
                    correl IN (SELECT correl AS correlativo FROM appfacturas WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fechad)) BETWEEN ? AND ? $chof)
                GROUP BY fecha_entre, observacion, tipo_pago, correl ORDER BY correl, fecha_entre";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$fechai);
        $sql->bindValue($i+=1,$fechaf);
        if($crechaz != "todos") {
            $sql->bindValue($i+=1, $crechaz);
        }
        if($id_chofer != "-") {
            $sql->bindValue($i+=1, $id_chofer);
        }
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_oportunidaddespacho_por_chofer($fechai, $fechaf, $id_chofer)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $sql= "SELECT numerod, codvend, descrip, fecha_entre,
                       (SELECT fechad FROM appfacturas WHERE appfacturas.correl=appfacturas_det.correl) AS fecha_desp,
                       (SELECT tiempo_estimado_despacho FROM SAVEND_02 WHERE SAVEND_02.CodVend=SAFACT.CodVend) AS tiempo_estimado
                FROM SAFACT INNER JOIN appfacturas_det ON appfacturas_det.numeros=SAFACT.NumeroD
                WHERE SAFACT.TipoFac IN ('A','C') AND correl IN (SELECT CORREL FROM appfacturas WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fechad))
                    BETWEEN ? AND ? AND cedula_chofer=?) ORDER BY NumeroD";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$fechai);
        $sql->bindValue(2,$fechaf);
        $sql->bindValue(3,$id_chofer);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_periodos($indicador, $tipo, $id_chofer)
    {
        $i = 0;
        $anio_in_curse = date('Y');
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        switch ($tipo) {
            case "Anual":
                $month_in_parameter = $month_in_condition = $month_in_groupBy = $month_in_orderBy = "";
                break;
            case "Mensual":
                $month_in_parameter = ", MONTH(CAST(fecha_entre AS DATETIME)) mes ";
                $month_in_condition = " DATEADD(dd, 0, DATEDIFF(dd, 0, fechad)) BETWEEN ? AND ? AND ";
                $month_in_groupBy = ", MONTH(CAST(fecha_entre AS DATETIME)) ";
                $month_in_orderBy = ", mes";
                break;
        }

        switch ($indicador) {
            case 1: $condition_indicador = ""; break;
            case 2: $condition_indicador = " AND observacion IS NOT NULL AND observacion != '' "; break;
            case 3: $condition_indicador = " AND SAFACT.TipoFac IN ('A','C') "; break;
        }

        $sql= "SELECT YEAR(CAST(fecha_entre AS DATETIME)) anio $month_in_parameter
                FROM appfacturas_det INNER JOIN SAFACT ON appfacturas_det.numeros = SAFACT.NumeroD WHERE
                correl IN (SELECT correl FROM appfacturas WHERE $month_in_condition cedula_chofer=?)
                AND fecha_entre IS NOT NULL $condition_indicador
                GROUP BY YEAR(CAST(fecha_entre AS DATETIME)) $month_in_groupBy
                ORDER BY anio DESC $month_in_orderBy";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        if($tipo=="Mensual") {
            $sql->bindValue($i+=1, $anio_in_curse.'-01-01');
            $sql->bindValue($i+=1, $anio_in_curse.'-12-31');
        }
        $sql->bindValue($i+=1,$id_chofer);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

}
