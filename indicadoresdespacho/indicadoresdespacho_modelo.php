
<?php
//LLAMAMOS A LA CONEXION.
require_once("../acceso/conexion.php");

class InidicadoresDespachos extends Conectar{

    public function get_correlativos_entregasefectivas_por_chofer($fechai, $fechaf, $id_chofer)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        /*
         -- fechaentrega_y_cantidaddespachados_por_chofer
        SELECT appfacturas_det.correl as entreg, fecha_entre, count(appfacturas_det.correl) as cant_documentos,
               (select CONCAT(cedula, ' - ', descripcion) from appChofer where cedula='16395823') as chofer
        FROM appfacturas_det where correl in
        (
        SELECT correl as correlativo
        FROM appfacturas where DATEADD(dd, 0, DATEDIFF(dd, 0, fechad)) between '2020-01-01' and '2020-11-20' and cedula_chofer='16395823'
        )
        and tipo_pago!='N/C' and fecha_entre is not null group by fecha_entre, correl order by fecha_entre

        --total despacho
        select sum(tmp.despachoxfecha) as totaldespacho from
        (select count(ad.numeros) as despachoxfecha from appfacturas inner join appfacturas_det ad on appfacturas.correl = ad.correl
         where DATEADD(dd, 0, DATEDIFF(dd, 0, fechad)) between '2020-01-01' and '2020-11-20' and cedula_chofer = '16395823' group by fecha_entre
        ) as tmp
         */

        //QUERY
       /* $sql= "SELECT Correlativo AS correlativo,
                       (SELECT COUNT(Despachos_Det.ID_Correlativo) FROM Despachos_Det WHERE Despachos_Det.ID_Correlativo = Despachos.Correlativo) AS totaldespacho,
                       (SELECT Nomper FROM Choferes WHERE cedula = ?) AS chofer
                FROM Despachos WHERE DATEADD(DD, 0, DATEDIFF(DD, 0, fechad)) BETWEEN ? AND ? AND ID_Chofer = ? ORDER BY Correlativo";*/

        $sql= "SELECT correl as correlativo,
                   (select COUNT(appfacturas_det.correl) from appfacturas_det where appfacturas_det.correl=appfacturas.correl) as totaldespacho,
                   (select CONCAT(cedula, ' - ', descripcion) from appChofer where cedula=?) as chofer
            FROM appfacturas where DATEADD(dd, 0, DATEDIFF(dd, 0, fechad)) between ? and ? and cedula_chofer='16395823' order by correl";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$id_chofer);
        $sql->bindValue(2,$fechai);
        $sql->bindValue(3,$fechaf);
        $sql->bindValue(4,$id_chofer);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_dias_entrega_por_orden_despacho($correlativo)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        /*$sql= "SELECT COUNT(Despachos_Det.ID_Correlativo) AS entreg, fecha_entre
                FROM Despachos_Det WHERE ID_Correlativo = ? AND tipo_pago != 'N/C' AND fecha_entre IS NOT NULL GROUP BY fecha_entre ORDER BY fecha_entre";*/

        $sql= "SELECT count(appfacturas_det.correl) as entreg, fecha_entre FROM appfacturas_det where correl=? and tipo_pago!='N/C' and fecha_entre is not null group by fecha_entre order by fecha_entre asc";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$correlativo);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_facturas_sin_liquidar_por_orden_despacho($correlativo)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
//        $sql= "SELECT numerod FROM Despachos_Det WHERE ID_Correlativo = ? AND fecha_entre IS NULL";
        $sql= "select numeros as numerod from appfacturas_det where correl=? and fecha_entre is NULL";



        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$correlativo);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
