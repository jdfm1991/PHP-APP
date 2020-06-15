<?php
//LLAMAMOS A LA CONEXION.
require_once("../acceso/conexion.php");

class DespachosRelacion extends Conectar{

    public function getRelacionDespachos(){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql= "SELECT TOP(200) *, 
                        (SELECT COUNT(Numerod) FROM Despachos_Det WHERE ID_Correlativo = Despachos.Correlativo) AS cantFact, 
	                    (SELECT Nomper from Choferes where Cedula = Despachos.ID_Chofer) AS NomperChofer 
                FROM Despachos INNER JOIN Usuarios ON Despachos.ID_Usuario = Usuarios.Cedula ORDER BY Correlativo DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }

    public function get_despacho_por_correlativo($correlativo){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql= "SELECT Correlativo, fechae, fechad, Destino, ID_Chofer, ID_Vehiculo,
                    (SELECT Nomper FROM Choferes WHERE Choferes.Cedula = Despachos.ID_Chofer) AS NomperChofer, 
                    (SELECT COUNT(ID_Correlativo) FROM Despachos_Det WHERE Despachos_Det.ID_Correlativo = Despachos.Correlativo) AS cantFacturas,
                    Vehiculos.Placa, Vehiculos.Modelo, Vehiculos.Capacidad
                     FROM Despachos INNER JOIN Vehiculos ON Vehiculos.ID = Despachos.ID_Vehiculo WHERE Correlativo = ?";

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
        $sql = "SELECT ID_Correlativo, Despachos_Det.Numerod, codclie, descrip, fechae, monto 
                    FROM Despachos_Det inner join [AJ].dbo.safact ON Despachos_Det.numerod = safact.numerod 
                    WHERE ID_Correlativo = ? and Despachos_Det.tipofac = 'A' and safact.tipofac = 'A' 
                    ORDER BY ID_Correlativo";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$correlativo, PDO::PARAM_STR);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

}