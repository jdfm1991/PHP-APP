
<?php
//LLAMAMOS A LA CONEXION.
require_once("../acceso/conexion.php");

class RelacionClientes extends Conectar
{

    public function get_todos_los_clientes()
    {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT saclie.codclie, saclie.descrip, saclie.id3, saclie.direc1 AS direccion, saclie.telef AS telefono, saclie.movil AS movil, saclie.diascred AS diascredito, saclie.limitecred AS limitecredito, saclie.descto AS descuento, saclie.CodVend AS edv, saclie.fechae AS fechae, limitecred, 
                    (SELECT SUM (saldo) FROM saacxc WHERE saacxc.codclie=saclie.codclie AND tipocxc='10' and saacxc.saldo>0) 
                    AS saldo, 
                    (SELECT count (numerod) FROM saacxc WHERE saacxc.codclie=saclie.codclie AND tipocxc='10') 
                    AS facturas
                    FROM saclie 
                    WHERE saclie.activo ='1' ORDER BY saclie.codclie ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_estados()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT estado, descrip FROM saestado WHERE pais =1 ORDER BY descrip ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_ciudad($estado)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT ciudad, descrip FROM saciudad WHERE estado = ? AND pais = '1' ORDER BY descrip ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $estado, PDO::PARAM_STR);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_zona()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT codzona, descrip FROM sazona WHERE codzona != 'codzona'  ORDER BY descrip ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_Edv()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT codvend, descrip FROM savend ORDER BY descrip ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_Cnestle()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT codnestle, descripcion FROM sanestle where codnestle != 'codnestle'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}