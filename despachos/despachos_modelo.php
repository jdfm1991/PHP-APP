
<?php
 //LLAMAMOS A LA CONEXION.
require_once("../acceso/conexion.php");

class Despachos extends Conectar{

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
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT numeros FROM appfacturas_det WHERE numeros = ?";

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

}

