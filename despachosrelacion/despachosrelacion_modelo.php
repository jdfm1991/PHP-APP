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
}