<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class DespachosRelacion extends Conectar{

    public function getRelacionDespachos() {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql= "SELECT correlativo, fechae, usuarios.nomper, destino, Choferes.Nomper AS NomperChofer, COUNT(Despachos_Det.Numerod) AS cantDocumentos
                FROM Despachos
                    INNER JOIN Usuarios ON Despachos.ID_Usuario = Usuarios.Cedula
                    INNER JOIN Choferes ON Despachos.ID_Chofer = Choferes.Cedula
                    INNER JOIN Despachos_Det ON Despachos_Det.ID_Correlativo = Despachos.Correlativo
                GROUP BY correlativo, fechae, usuarios.nomper, destino, Choferes.Nomper
                ORDER BY Correlativo DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_despacho_por_correlativo($correlativo){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql= "SELECT correlativo, fechae, fechad, destino, Choferes.Nomper AS NomperChofer, Vehiculos.placa, Vehiculos.modelo, Vehiculos.capacidad,
                       ID_Chofer, ID_Vehiculo, COUNT(Despachos_Det.ID_Correlativo)  AS cantDocumentos
                FROM Despachos
                         INNER JOIN Vehiculos ON Vehiculos.ID = Despachos.ID_Vehiculo
                         INNER JOIN Choferes ON Despachos.ID_Chofer = Choferes.Cedula
                         INNER JOIN Despachos_Det ON Despachos_Det.ID_Correlativo = Despachos.Correlativo
                WHERE Correlativo = ?
                GROUP BY correlativo, fechae, fechad, destino, Choferes.Nomper, Vehiculos.Placa, Vehiculos.Modelo, Vehiculos.Capacidad, ID_Chofer, ID_Vehiculo";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $correlativo);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }

    public function get_detalle_despacho_por_correlativo($correlativo) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "SELECT numerod, tipofac FROM Despachos_Det WHERE ID_Correlativo = ? ";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$correlativo);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    public function get_documentos_por_correlativo($correlativo) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "SELECT * FROM Despachos_Det where ID_Correlativo = ? AND Activo = '1'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$correlativo);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_factura_en_despacho($correlativo, $nro_documento) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "SELECT * FROM Despachos_Det where ID_Correlativo = ?  AND Numerod = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$correlativo);
        $sql->bindValue(2,$nro_documento);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_factura_de_un_despacho_por_correlativo($correlativo) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT NumeroD, FechaE, CodVend, CodClie, Descrip, 
                    (SELECT SUM(CASE WHEN EsUnid = 0 THEN (saprod.Tara*Cantidad) ELSE ((saprod.Tara/CantEmpaq)*Cantidad) END) FROM saitemfac INNER JOIN saprod ON saitemfac.coditem = saprod.codprod WHERE numerod = SAFACT.NumeroD AND TIPOFAC = 'A') 
                    AS Peso
                    FROM SAFACT WHERE (NumeroD IN ( SELECT Numerod FROM [APPWEBAJ].dbo.Despachos_Det WHERE ID_Correlativo = ? )) AND TipoFac = 'A' ORDER BY NumeroD";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$correlativo);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_productos_devueltos_de_un_despacho($correlativo) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT DISTINCT CodItem, Descrip,

                    COALESCE((SELECT SUM(Cantidad) FROM SAITEMFAC WHERE CodItem = SAPROD.CodProd AND
                    EsUnid = 0 AND TipoFac = 'B' AND OTipo = 'A' AND (ONumero IN ( SELECT Numerod FROM [APPWEBAJ].dbo.Despachos_Det WHERE ID_Correlativo = ? ))), 0)
                    AS BULTOS,
                    COALESCE((SELECT SUM(Cantidad) FROM SAITEMFAC WHERE CodItem = SAPROD.CodProd AND
                    EsUnid = 1 AND TipoFac = 'B' AND OTipo = 'A' AND (ONumero IN ( SELECT Numerod FROM [APPWEBAJ].dbo.Despachos_Det WHERE ID_Correlativo = ? ))), 0)
                    AS PAQUETES,
                    CantEmpaq,
                    EsEmpaque,
                    saprod.Tara AS tara,
                    CodInst
                    
                    FROM SAITEMFAC INNER JOIN SAPROD ON SAITEMFAC.CodItem = SAPROD.CodProd WHERE
                    TipoFac = 'B' AND OTipo = 'A' AND (ONumero IN ( SELECT Numerod FROM [APPWEBAJ].dbo.Despachos_Det WHERE ID_Correlativo = ? )) ORDER BY SAITEMFAC.CodItem";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$correlativo);
        $sql->bindValue(2,$correlativo);
        $sql->bindValue(3,$correlativo);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_facturas_devueltas_de_un_despacho($correlativo) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT DISTINCT ONumero
				FROM SAITEMFAC INNER JOIN SAPROD ON SAITEMFAC.CodItem = SAPROD.CodProd 
				WHERE TipoFac = 'B' AND OTipo = 'A'  AND (ONumero IN ( SELECT Numerod FROM [APPWEBAJ].dbo.Despachos_Det WHERE ID_Correlativo = ? )) 
				ORDER BY SAITEMFAC.ONumero";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$correlativo);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}