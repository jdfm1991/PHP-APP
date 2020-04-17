<?php
//LLAMAMOS A LA CONEXION.
require_once("../acceso/conexion.php");

class Despachos extends Conectar{

    public function listar_despachos(){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT TOP 200 despachos.correlativo AS id_correlativo, despachos.id_usuario AS id_usuario, despachos.fechae AS fechae, despachos.fechad AS fechad, despachos.id_chofer AS id_chofer, despachos.destino AS destino, usuarios.nomper AS nomper  FROM despachos INNER JOIN usuarios ON despachos.id_usuario = usuarios.cedula ORDER BY correlativo DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }
}
