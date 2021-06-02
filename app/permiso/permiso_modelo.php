
<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Permiso extends Conectar
{
    public function registrar_rolmod($data)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO Roles_Grupo(ID_Modulo, ID_Roles) VALUES(?,?);";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $data["modulo_id"]);
        $sql->bindValue($i+=1, $data["rol_id"]);

        return $sql->execute();
    }

    public function borrar_rolmod($data)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "DELETE FROM Roles_Grupo WHERE ID_Modulo=? AND ID_Roles=?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $data["modulo_id"]);
        $sql->bindValue($i+=1, $data["rol_id"]);

        return $sql->execute();
    }

}
