<?php
//LLAMAMOS A LA CONEXION.
require_once("../acceso/conexion.php");

class Kpi extends Conectar
{
    public function get_marcas_kpi()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT id, descripcion FROM Kpi_marcas";
        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_coordinadores()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT DISTINCT coordinador
                FROM savend_02 d INNER JOIN savend S ON S.codvend = d.CodVend
                WHERE (d.coordinador = '' OR d.coordinador IS NOT NULL) AND d.coordinador != ' ' AND S.Activo = 1 AND s.codvend != '00' AND s.codvend != '16'
                ORDER BY coordinador ASC";
        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_rutasPorCoordinador($nombre)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT * FROM savend INNER JOIN savend_02 ON savend.codvend = savend_02.codvend
                WHERE activo = '1' AND coordinador != '' AND savend_02.coordinador LIKE ?
                ORDER BY savend.codvend";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $nombre);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

}