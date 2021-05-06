<?php

//conexion a la base de datos

//require_once("../config/conexion.php");


class Choferes extends Conectar
{

    public function get_filas_choferes()
    {

        $conectar = parent::conexion();

        $sql = "select * from choferes";

        $sql = $conectar->prepare($sql);
        $sql->execute();

        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        return $sql->rowCount();
    }


    public function registrar_chofer($cedula, $nomper, $estado)
    {

        $conectar = parent::conexion();
        parent::set_names();

        $nomper = ucwords($_POST["nomper"]);


        $sql = "INSERT INTO choferes VALUES(?,?,getdate(),?);";

        $sql = $conectar->prepare($sql);

        $sql->bindValue(1, $_POST["cedula"]);
        $sql->bindValue(2, $nomper);
        $sql->bindValue(3, $_POST["estado"]);
        $sql->execute();


    }

    public function editar_chofer($nomper, $estado, $id_chofer)
    {

        $conectar = parent::conexion();
        parent::set_names();

        $nomper = ucwords($_POST["nomper"]);

        $sql = "UPDATE choferes SET  Nomper=?,  Estado=?  WHERE   Cedula=?";

//echo $sql; exit();

        $sql = $conectar->prepare($sql);

        $sql->bindValue(1, $nomper);
        $sql->bindValue(2, $_POST["estado"]);
        $sql->bindValue(3, $_POST["id_chofer"]);
        $sql->execute();

    }

    public function editar_estado($id, $estado)
    {

        $conectar = parent::conexion();
        parent::set_names();
//el parametro est se envia por via ajax
        if ($_POST["est"] == "0") {
            $estado = 1;
        } else {
            $estado = 0;
        }

        $sql = "UPDATE choferes SET estado=? WHERE cedula=?";

        $sql = $conectar->prepare($sql);

        $sql->bindValue(1, $estado);
        $sql->bindValue(2, $id);
        $sql->execute();
    }

    public function get_cedula_del_chofer($cedula)
    {

        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT * FROM Choferes WHERE cedula=? ";

        $sql = $conectar->prepare($sql);

        $sql->bindValue(1, $cedula);
        $sql->execute();

        return $resultado = $sql->fetchAll();

    }


}
