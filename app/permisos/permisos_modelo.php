
<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Permiso extends Conectar
{

    public function getRolesGrupoPorRolID($rol_id)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        //QUERY
        $sql =   "SELECT id, id_modulo, id_roles FROM Roles_Grupo WHERE id_roles=?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $rol_id);
        $sql->execute();

        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
