<?php
//LLAMAMOS A LA CONEXION.
require_once("../acceso/conexion.php");

class KpiManager extends Conectar {

    public function get_datos_edv($edv)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $condicion = ($edv!='-') ? 'WHERE U.CodVend = ?' : '';

        //QUERY
        $sql = "SELECT S.Descrip, S.clase, S.activo, U.* FROM savend_02 AS U INNER JOIN savend AS S ON S.CodVend = U.CodVend $condicion";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        if ($edv!='-')
            $sql->bindValue(1, $edv);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
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
}