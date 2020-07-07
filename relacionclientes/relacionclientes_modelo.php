
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
                    (SELECT COALESCE(SUM(saldo), 0) FROM saacxc WHERE saacxc.codclie=saclie.codclie AND tipocxc='10' and saacxc.saldo>0) 
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

    public function get_cliente_por_id($codclie)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT COUNT(codclie) AS cont FROM [APPWEBAJ].dbo.Saclie_Ext WHERE codclie = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codclie, PDO::PARAM_STR);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);

        //QUERY_1
        if ($result[0]['cont'] != "0") {
            $sql1 = "SELECT cli.codclie AS codigo, cli.descrip AS descrip, cli.id3 AS id3, cli.clase AS clase, cli.represent AS represent, cli.direc1 AS direc1, cli.direc2 AS direc2, cli.estado AS idestado, cli.ciudad AS idciudad, cli.telef AS telef, cli.codzona AS idzona, cli.codvend AS idvend, cli.tipocli AS idtcli, cli.tipopvp AS idtpvp, cli.escredito AS credito, cli.limitecred AS lcred, cli.diascred AS dcred, cli.estoleran AS toleran, cli.diastole AS dtoleran, cli.descto AS descto, cli.activo AS idactivo, cli.movil AS movil, cli.tipoid3 AS idtid3, cli.observa AS observa, (SELECT descrip FROM saestado WHERE estado=cli.estado) AS estado, (SELECT descrip FROM saciudad WHERE ciudad=cli.ciudad) AS ciudad, (SELECT descrip FROM sazona WHERE codzona=cli.codzona) AS zona, (SELECT descrip FROM savend WHERE codvend=cli.codvend) AS vend, cli2.Dia_Visita AS dvisitas, cli2.codnestle AS idnestle, cli2.ruc AS ruc, cli2.Municipio AS municipio, (SELECT descripcion FROM sanestle WHERE codnestle=cli2.codnestle) AS nestle
                FROM saclie AS cli, [APPWEBAJ].dbo.Saclie_Ext AS cli2
                WHERE cli.CodClie = ? AND cli.CodClie=cli2.CodClie";
        } else {
            $sql1 = "SELECT cli.codclie AS codigo, cli.descrip AS descrip, cli.id3 AS id3, cli.clase AS clase, cli.represent AS represent, cli.direc1 AS direc1, cli.direc2 AS direc2, cli.estado AS idestado, cli.ciudad AS idciudad, cli.telef AS telef, cli.codzona AS idzona, cli.codvend AS idvend, cli.tipocli AS idtcli, cli.tipopvp AS idtpvp, cli.escredito AS credito, cli.limitecred AS lcred, cli.diascred AS dcred, cli.estoleran AS toleran, cli.diastole AS dtoleran, cli.descto AS descto, cli.activo AS idactivo, cli.movil AS movil, cli.tipoid3 AS idtid3, cli.observa AS observa, (SELECT descrip FROM saestado WHERE estado=cli.estado) AS estado, (SELECT descrip FROM saciudad WHERE ciudad=cli.ciudad) AS ciudad, (SELECT descrip FROM sazona WHERE codzona=cli.codzona) AS zona, (SELECT descrip FROM savend WHERE codvend=cli.codvend) AS vend 
                    FROM saclie AS cli 
                    WHERE cli.CodClie = ?";
        }

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql1 = $conectar->prepare($sql1);
        $sql1->bindValue(1, $codclie, PDO::PARAM_STR);
        $sql1->execute();
        return $result1 = $sql1->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_estados()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT estado, descrip FROM saestado WHERE pais = 1 ORDER BY descrip ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_ciudades_por_estado($estado)
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
        $sql = "SELECT codnestle, descripcion as descrip FROM sanestle where codnestle != 'codnestle'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

}