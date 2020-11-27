
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
                COALESCE(STUFF((SELECT ',' + a.numeros FROM appfacturas_det AS a WHERE a.correl=det.correl
                                AND fecha_entre IS NULL FOR XML PATH ('')), 1, 2, ''), '') AS fact_sin_liquidar,
                (SELECT CONCAT(cedula, ' - ', descripcion) FROM appChofer WHERE cedula = ?) AS chofer
                FROM appfacturas_det AS det WHERE correl IN
                (
                    SELECT correl AS correlativo 
                    FROM appfacturas WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fechad)) BETWEEN ? AND ? AND cedula_chofer = ?
                )
                GROUP BY fecha_entre, tipo_pago, correl ORDER BY fecha_entre";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$id_chofer);
        $sql->bindValue(2,$fechai);
        $sql->bindValue(3,$fechaf);
        $sql->bindValue(4,$id_chofer);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

}
