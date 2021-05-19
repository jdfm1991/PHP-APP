<?php

//conexion a la base de datos
require_once("../../config/conexion.php");

class Vehiculos extends Conectar
{

    //listar los usuarios
    public function get_vehiculos()
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT id, placa, modelo, capacidad, volumen, fecha_registro, estado 
            FROM Vehiculos WHERE deleted_at IS NULL";

        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }


    public function registrar_vehiculo($data)
    {
        $i = 0;
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO vehiculos(Placa, Modelo, Capacidad, Volumen, Fecha_Registro, Estado) VALUES(?,?,?,?,?,?);";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i += 1, $data["placa"]);
        $sql->bindValue($i += 1, $data["modelo"]);
        $sql->bindValue($i += 1, $data["capacidad"]);
        $sql->bindValue($i += 1, $data["volumen"]);
        $sql->bindValue($i += 1, date("Y/m/d h:i:s"));
        $sql->bindValue($i += 1, $data["estado"]);

        return $sql->execute();
    }

    public function editar_vehiculo($data)
    {
        $i = 0;
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE vehiculos SET  Modelo=?,  Capacidad=?,  Volumen=?,  Estado=?  WHERE   ID=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i += 1, $data["modelo"]);
        $sql->bindValue($i += 1, $data["capacidad"]);
        $sql->bindValue($i += 1, $data["volumen"]);
        $sql->bindValue($i += 1, $data["estado"]);
        $sql->bindValue($i += 1, $data["id_vehiculo"]);

        return $sql->execute();
    }

//fin editar usuario

//mostrar los datos del usuario por el id
    public function get_vehiculo_por_id($id)
    {

        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT id, placa, modelo, capacidad, volumen, fecha_registro, estado 
                FROM Vehiculos WHERE deleted_at IS NULL AND id=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $id);
        $sql->execute();

        return $resultado = $sql->fetchAll();

    }

    public function editar_estado($id, $estado)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE vehiculos SET estado=? WHERE id=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $estado);
        $sql->bindValue(2, $id);

        return $sql->execute();
    }

    public function get_placa_del_vehiculo($placa)
    {

        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT id, placa, modelo, capacidad, volumen, fecha_registro, estado 
                FROM Vehiculos WHERE deleted_at IS NULL AND placa=? ";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $placa);
        $sql->execute();
        return $resultado = $sql->fetchAll();

    }

    public function eliminar_vehiculo($id)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        //QUERY
//    $sql = "DELETE FROM vehiculos WHERE id = ?";
        $sql = "UPDATE vehiculos SET deleted_at=? WHERE id=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, date("Y/m/d h:i:s"));
        $sql->bindValue(2, $id);

        return $sql->execute();
    }
}

?>
