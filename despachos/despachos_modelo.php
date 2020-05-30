
<?php
 //LLAMAMOS A LA CONEXION.
require_once("../acceso/conexion.php");

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
        $sql = "SELECT saitemfac.coditem AS cod_prod, saprod.tara AS peso, saitemfac.esunid AS unidad, saprod.cantempaq AS paquetes, saitemfac.cantidad AS cantidad, tipofac AS tipofac
                FROM saitemfac INNER JOIN saprod ON saitemfac.coditem = saprod.codprod 
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
                FROM SAFACT WHERE NumeroD = ? AND TipoFac = 'A'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$numero_fact);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNuevoCorrelativo() {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "SELECT TOP(1) Correlativo AS correl FROM Despachos ORDER BY Correlativo DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarDespacho($fechad, $chofer, $vehiculo, $destino, $usuario) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "INSERT INTO Despachos (fechae, fechad, ID_Chofer, ID_Vehiculo, Destino, ID_Usuario) VALUES (GETDATE(), ?, ?, ?, ?, ?)";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$fechad);
        $sql->bindValue(2,$chofer);
        $sql->bindValue(3,$vehiculo);
        $sql->bindValue(4,$destino);
        $sql->bindValue(5,$usuario);
        return $sql->execute();
    }

    public function insertarDetalleDespacho($correlativo, $numero_documento, $tipo_documento) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "INSERT INTO Despachos_Det (ID_Correlativo, Numerod, Tipofac) VALUES (?, ?, ?)";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$correlativo);
        $sql->bindValue(2,$numero_documento);
        $sql->bindValue(3,$tipo_documento);
        $sql->execute();
    }

    public function getProductosDespachoCreado($documentos) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT DISTINCT CodItem, Descrip, 

                COALESCE((SELECT SUM(Cantidad) FROM SAITEMFAC WHERE CodItem = SAPROD.CodProd 
                AND TipoFac = 'A' AND EsUnid = '0' AND (numerod in ( ? ))), 0)
                AS BULTOS,
                COALESCE((SELECT SUM(Cantidad) FROM SAITEMFAC WHERE CodItem = SAPROD.CodProd 
                AND TipoFac = 'A' AND EsUnid = '1' AND (numerod in ( ? ))), 0)
                AS PAQUETES,
                CantEmpaq,
                EsEmpaque 
                FROM SAITEMFAC INNER JOIN SAPROD ON SAITEMFAC.CodItem = SAPROD.CodProd WHERE 
                TipoFac = 'A' AND (numerod in ( ? )) order by SAITEMFAC.CodItem";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$documentos, PDO::PARAM_STR);
        $sql->bindValue(2,$documentos, PDO::PARAM_STR);
        $sql->bindValue(3,$documentos, PDO::PARAM_STR);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}

