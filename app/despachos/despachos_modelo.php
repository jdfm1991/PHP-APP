<?php
set_time_limit(0);
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Despachos extends Conectar{

    public function getDatosEmpresa() {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT Descrip, Direc1, telef, rif FROM SACONF";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCabeceraDespacho($correlativo) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "SELECT Correlativo, fechae, fechad, ID_Chofer, ID_Vehiculo, Destino, ID_Usuario FROM Despachos where Correlativo = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $correlativo);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPesoTotalporFactura($numero_fact) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT saitemfac.coditem AS cod_prod, saprod.tara AS peso, saitemfac.esunid AS unidad, saprod.cantempaq AS paquetes, saitemfac.cantidad AS cantidad, tipofac AS tipofac, Cubicaje AS cubicaje
                FROM saitemfac INNER JOIN saprod ON saitemfac.coditem = saprod.codprod INNER JOIN SAPROD_01 ON SAPROD.CodProd = SAPROD_01.CodProd
                WHERE numerod = ? AND TIPOFAC = 'A' ORDER BY saitemfac.coditem";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$numero_fact);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getExisteFacturaEnDespachos($numero_fact) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
//        $sql = "SELECT numeros FROM appfacturas_det WHERE numeros = ?";
        $sql = "SELECT Numerod FROM Despachos_Det WHERE Numerod = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$numero_fact);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFactura($numero_fact) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT NumeroD AS numerod, FechaE AS fechae, Descrip AS descrip, Direc2 AS direc2, CodVend AS codvend, MtoTotal AS mtototal 
                FROM SAFACT WHERE NumeroD = ? AND TipoFac IN ('A','C')";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$numero_fact);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarDespacho($values) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "INSERT INTO Despachos (fechae, fechad, ID_Chofer, ID_Vehiculo, Destino, ID_Usuario) VALUES (?, ?, ?, ?, ?, ?)";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, date('Y-m-d'));
        $sql->bindValue($i+=1,$values['fechad']);
        $sql->bindValue($i+=1,$values['chofer']);
        $sql->bindValue($i+=1,$values['vehiculo']);
        $sql->bindValue($i+=1,$values['destino']);
        $sql->bindValue($i+=1,$values['usuario']);
        $resultado = $sql->execute();

        return $resultado ? $conectar->lastInsertId() : -1;
    }

    public function deleteDespacho($correlativo) {

        $delete1 = false;
        $delete2 = false;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql_1 = "DELETE FROM Despachos WHERE Correlativo = ?";
        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql_1 = $conectar->prepare($sql_1);
        $sql_1->bindValue(1,$correlativo);
        $delete1 = $sql_1->execute();

        //QUERY
        $sql_2 = "DELETE FROM Despachos_Det WHERE ID_Correlativo = ?";
        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql_2 = $conectar->prepare($sql_2);
        $sql_2->bindValue(1,$correlativo);
        $delete2 = $sql_2->execute();


        return ($delete1 && $delete2);
    }

    public function insertarDetalleDespacho($correlativo, $numero_documento, $tipo_documento, $estado = null) {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        if (is_null($estado)) {
            $sql = "INSERT INTO Despachos_Det (ID_Correlativo, Numerod, Tipofac) VALUES (?, ?, ?)";
        } else {
            $sql = "INSERT INTO Despachos_Det (ID_Correlativo, Numerod, Estado, Tipofac) VALUES (?, ?, ?, ?)";
        }

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$correlativo);
        $sql->bindValue($i+=1,$numero_documento);
        if (!is_null($estado)) {
            $sql->bindValue($i += 1, $estado);
        }
        $sql->bindValue($i+=1,$tipo_documento);
        return $sql->execute();
    }

    public function updateDespacho($correlativo, $destino, $chofer, $vehiculo, $fechad) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "UPDATE Despachos SET Destino = ?,  ID_Chofer = ?,  ID_Vehiculo = ?,  fechad = ? WHERE Correlativo = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$destino);
        $sql->bindValue(2,$chofer);
        $sql->bindValue(3,$vehiculo);
        $sql->bindValue(4,$fechad);
        $sql->bindValue(5,$correlativo);
        return $sql->execute();
    }

    public function updateDetalleDespacho($correlativo, $nuevo, $viejo) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "UPDATE Despachos_det SET Numerod = ? WHERE ID_Correlativo = ? AND Numerod = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$nuevo);
        $sql->bindValue(2,$correlativo);
        $sql->bindValue(3,$viejo);
        return $sql->execute();
    }

    public function deleteDetalleDespacho($correlativo, $documento) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "DELETE FROM Despachos_Det WHERE ID_Correlativo = ? and Numerod = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$correlativo);
        $sql->bindValue(2,$documento);
        return $sql->execute();
    }

    public function get_despacho_por_id($correlativo) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "SELECT * FROM Despachos WHERE Correlativo = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$correlativo, PDO::PARAM_STR);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_existe_factura_despachada_por_id($numerod) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "SELECT fechad, correlativo, 
                (SELECT Nomper FROM Usuarios WHERE Cedula = Despachos.ID_Usuario) 
                AS nomper 
                FROM Despachos_Det INNER JOIN Despachos ON Despachos_Det.id_correlativo = Despachos.correlativo WHERE Despachos_Det.numerod = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$numerod, PDO::PARAM_STR);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductosDespachoCreado($correlativo) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT DISTINCT CodItem, Descrip, CantEmpaq, EsEmpaque, saprod.Tara as tara, CodInst,
                    COALESCE((SELECT SUM(Cantidad) FROM SAITEMFAC WHERE CodItem = SAPROD.CodProd
                    AND TipoFac IN ('A','C') AND EsUnid = '0' AND (numerod in ( SELECT Numerod FROM [APPWEBAJ].dbo.Despachos_Det WHERE ID_Correlativo = ? ))), 0)
                    AS BULTOS,
                    COALESCE((SELECT SUM(Cantidad) FROM SAITEMFAC WHERE CodItem = SAPROD.CodProd
                    AND TipoFac IN ('A','C') AND EsUnid = '1' AND (numerod in ( SELECT Numerod FROM [APPWEBAJ].dbo.Despachos_Det WHERE ID_Correlativo = ? ))), 0)
                    AS PAQUETES
                FROM SAITEMFAC INNER JOIN SAPROD ON SAITEMFAC.CodItem = SAPROD.CodProd 
                WHERE TipoFac IN ('A','C') AND (numerod in ( SELECT Numerod FROM [APPWEBAJ].dbo.Despachos_Det WHERE ID_Correlativo = ? )) 
                ORDER BY SAITEMFAC.CodItem";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$correlativo);
        $sql->bindValue($i+=1,$correlativo);
        $sql->bindValue($i+=1,$correlativo);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFacturasPorCorrelativo($correlativo) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "SELECT Numerod FROM Despachos_Det WHERE ID_Correlativo = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$correlativo);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFacturaEnDespachos($documento){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql= "SELECT Correlativo, fechae, Destino, fecha_liqui, monto_cancelado,    
                    (SELECT Nomper from Choferes where Choferes.Cedula = Despachos.ID_Chofer) AS NomperChofer 
                    FROM Despachos INNER JOIN Despachos_Det ON Despachos.Correlativo = Despachos_Det.ID_Correlativo WHERE Despachos_Det.Numerod = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $documento);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getMercanciaSinDespachar($fechai, $fechaf, $alm=array()){
        $i=0;
        $cond = $depo = "";
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        if (count($alm) > 0) {
            $aux = "";
            //se contruye un string para listar los depositvos seleccionados
            //en caso que no haya ninguno, sera vacio
            foreach ($alm as $num)
                $aux .= " OR CodUbic = ?";

            //armamos una lista de los depositos, si no existe ninguno seleccionado no se considera para realizar la consulta
            $depo = "(" . substr($aux, 4, strlen($aux)) . ")";

            $cond = ($depo != "()")
                ? ("AND ".$depo)
                : "";
        }

        //QUERY
        $sql= "SELECT CodProd, Descrip, CantEmpaq,
                    (SELECT SUM(cantidad) FROM SAITEMFAC WHERE esunid='0' AND CodProd=CodItem ".$cond." AND numerod IN (SELECT fa.numerod FROM SAFACT AS fa WHERE TipoFac='A' AND
                    DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? AND (NumeroR IS NULL OR NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? GROUP BY x.NumeroR HAVING cast(SUM(x.Monto) AS BIGINT)<cast(fa.Monto AS BIGINT))) AND NumeroD NOT IN (SELECT Despachos_Det.Numerod FROM APPWEBAJ.dbo.Despachos_Det)))
                    AS todob,
                    (SELECT SUM(cantidad) FROM SAITEMFAC WHERE esunid='1' AND CodProd=CodItem ".$cond." AND numerod IN (SELECT fa.numerod FROM SAFACT AS fa WHERE TipoFac='A' AND
                    DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? AND (NumeroR IS NULL OR NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? GROUP BY x.NumeroR HAVING cast(SUM(x.Monto) AS BIGINT)<cast(fa.Monto AS BIGINT))) AND NumeroD NOT IN (SELECT Despachos_Det.Numerod FROM APPWEBAJ.dbo.Despachos_Det)))
                    AS todop,
                    (SELECT SUM(Existen) FROM SAEXIS WHERE CodProd=SAPROD.CodProd ".$cond.")
                    AS exis,
                    (SELECT SUM(ExUnidad) FROM SAEXIS WHERE CodProd=SAPROD.CodProd  ".$cond.")
                    AS exunid
                FROM SAPROD WHERE CodProd IN (SELECT coditem FROM SAITEMFAC WHERE TipoFac='A' ".$cond."
                AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ?) ORDER BY CodProd";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        if ($depo != "()") {
            foreach ($alm as $num)
                $sql->bindValue($i += 1, $num);
        }
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        if ($depo != "()") {
            foreach ($alm as $num)
                $sql->bindValue($i += 1, $num);
        }
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        if ($depo != "()") {
            foreach ($alm as $num)
                $sql->bindValue($i += 1, $num);
        }
        if ($depo != "()") {
            foreach ($alm as $num)
                $sql->bindValue($i += 1, $num);
        }
        if ($depo != "()") {
            foreach ($alm as $num)
                $sql->bindValue($i += 1, $num);
        }
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }
}

