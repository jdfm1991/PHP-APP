<?php
ini_set('memory_limit', '-1');
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Principal extends Conectar{

    public function getDocumentosSinDespachar(){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT *
                FROM safact AS SA INNER JOIN SAVEND AS VEND ON VEND.CodVend = SA.CodVend
                WHERE SA.NumeroD NOT IN (SELECT Despachos_Det.Numerod FROM APPWEBAJ.dbo.Despachos_Det)
                  AND SA.TipoFac = 'A'
                  AND (SA.NumeroR IS NULL OR SA.NumeroR IN (SELECT x.NumeroD FROM SAFACT AS x WHERE cast(x.Monto AS INT)<cast(SA.Monto AS INT) AND X.TipoFac = 'B'
                  AND x.NumeroD=SA.NumeroR))  AND SA.NumeroD NOT IN (SELECT numerof FROM sanota) ORDER BY SA.NumeroD";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPedidosSinFacturar(){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT *
                FROM SAITEMFAC AS a
                         INNER JOIN SAFACT AS b ON a.numerod = b.NumeroD
                WHERE a.TipoFac NOT IN ('C','A','G') and a.OTipo = 'F'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_cxc_bs(){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT SUM(SAACXC.Saldo) as saldo_bs
                FROM saacxc INNER JOIN saclie ON saacxc.codclie = saclie.codclie
                WHERE saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20')";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_cxc_dolares(){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT SUM(total-abono) as saldo_dolares
                FROM SANOTA
                WHERE tipofac ='C' AND estatus in (0, 1)";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_ventas_por_mes_fact($fechai, $fechaf) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT YEAR(CAST(FechaE AS DATETIME)) anio, MONTH(CAST(FechaE AS DATETIME)) mes,
                       SUM((TGravable/Tasa) * (CASE WHEN TipoFac = 'A' THEN 1 ELSE -1 END)) AS total
                FROM SAFACT
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? AND TipoFac in ('A','B')
                GROUP BY YEAR(CAST(FechaE AS DATETIME)), MONTH(CAST(FechaE AS DATETIME))
                ORDER BY mes ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_ventas_por_mes_nota($fechai, $fechaf) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT YEAR(CAST(FechaE AS DATETIME)) anio, MONTH(CAST(FechaE AS DATETIME)) mes,
                       SUM(total * (CASE WHEN TipoFac = 'C' THEN 1 ELSE -1 END)) as total
                FROM SANOTA
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? AND tipofac in ('C','D')
                GROUP BY YEAR(CAST(FechaE AS DATETIME)), MONTH(CAST(FechaE AS DATETIME))
                ORDER BY mes ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_inventario_valorizado($alm) {
        $i = 0;
        $cond = $depo = "";
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        if (count($alm) > 0) {
            $aux = "";
            //se contruye un string para listar los depositvos seleccionados
            //en caso que no haya ninguno, sera vacio
            foreach ($alm as $num)
                $aux .= " OR exis.codubic = ?";

            //armamos una lista de los depositos, si no existe ninguno seleccionado no se considera para realizar la consulta
            $depo = "(" . substr($aux, 4, strlen($aux)) . ")";

            $cond = ($depo != "()")
                ? ("AND ".$depo)
                : "";
        }

        //QUERY
        $sql = "SELECT depo.CodUbic AS almacen, SUM(exis.Existen * prod02.Precio1_B) AS total_b, SUM(exis.exunidad * prod02.Precio1_P) AS total_p
                FROM SADEPO depo
                    INNER JOIN SAEXIS exis ON depo.CodUbic = exis.CodUbic
                    INNER JOIN SAPROD prod ON exis.CodProd = prod.CodProd
                    INNER JOIN SAPROD_02 prod02 ON exis.CodProd = prod02.CodProd
                WHERE (exis.existen > 0 OR exis.exunidad > 0) AND len(prod.marca) > 0 $cond
                GROUP BY depo.CodUbic ORDER BY depo.CodUbic ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        if ($depo != "()") {
            foreach ($alm AS $num)
                $sql->bindValue($i+=1, $num);
        }
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}

