<?php
//LLAMAMOS A LA CONEXION.
require_once("../acceso/conexion.php");

class KpiMarcas extends Conectar{

    public function listar_kpiMarcas()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT id, descripcion, fechae FROM Kpi_marcas";

        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar_kpiMarcas($marca)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO Kpi_marcas (descripcion, fechae) VALUES (?,?)";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $marca);
        $sql->bindValue(2, date("d/m/Y h:i:s"));

        return $sql->execute();
    }

    public function eliminar_kpiMarcas()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "TRUNCATE TABLE Kpi_marcas";
        $sql = $conectar->prepare($sql);
        return $sql->execute();
    }

}