
<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Tabladinamica extends Conectar{

    public function getTabladinamicaFactura($data)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

         $fechai=$data['fechai'];
        $fechaf=$data['fechaf'];

        //QUERY
        $sql= "SELECT SACOMP.CodProv, SAPROV.Descrip,SAITEMCOM.TipoCom as tipo, SACOMP.NumeroD, SAITEMCOM.CodItem, SAITEMCOM.Descrip1, SAITEMCOM.Costo, 
SAITEMCOM.Cantidad, SAITEMCOM.TotalItem, SACOMP.FechaE, SACOMP_01.Tasa as tasa 
from SACOMP inner join SAITEMCOM on SAITEMCOM.NumeroD = SACOMP.NumeroD inner join SAPROV on SAPROV.CodProv= SAITEMCOM.CodProv
inner join SACOMP_01 on SACOMP_01.NumeroD = SACOMP.NumeroD where DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMCOM.FechaE)) between '$fechai' AND '$fechaf'  AND (SAITEMCOM.TipoCom = 'H' OR SAITEMCOM.TipoCom = 'I') ORDER BY SAITEMCOM.fechae asc";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                return $result ;
    }

    public function getTabladinamicaNotaDeEntrega($data)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $fechai=$data['fechai'];
        $fechaf=$data['fechaf'];

        //QUERY
        $sql= "SELECT SACOMP.CodProv, SAPROV.Descrip,SAITEMCOM.TipoCom as tipo, SACOMP.NumeroD, SAITEMCOM.CodItem, SAITEMCOM.Descrip1, SAITEMCOM.Costo, 
        SAITEMCOM.Cantidad, SAITEMCOM.TotalItem, SACOMP.FechaE, SACOMP_01.Tasa as tasa 
        from SACOMP inner join SAITEMCOM on SAITEMCOM.NumeroD = SACOMP.NumeroD inner join SAPROV on SAPROV.CodProv= SAITEMCOM.CodProv
        inner join SACOMP_01 on SACOMP_01.NumeroD = SACOMP.NumeroD where DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMCOM.FechaE)) between '$fechai' AND '$fechaf'  AND (SAITEMCOM.TipoCom = 'J' OR SAITEMCOM.TipoCom = 'K') ORDER BY SAITEMCOM.fechae asc";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
            $sql = $conectar->prepare($sql);
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                return $result ;
        
    }

    public function getTotalNotaDeEntrega($data, $tipo)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $edv = (!hash_equals('-', $data['edv'])) ? " AND saitemnota.codvend = ?" : "";

        //QUERY
        $sql= "SELECT sum((CASE SAITEMNOTA.esexento WHEN 1  THEN SAITEMNOTA.total ELSE SAITEMNOTA.total / 1.16 END)) montod
                FROM SAITEMNOTA INNER JOIN saprod ON SAITEMNOTA.coditem = saprod.codprod
                INNER JOIN sanota ON saitemnota.numerod = sanota.numerod AND saitemnota.tipofac = sanota.tipofac
                WHERE
                DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMNOTA.FechaE)) BETWEEN ? AND ? $edv AND (SAITEMNOTA.tipofac = ?) AND  
                SANOTA.numerof = (SELECT numerof FROM sanota WHERE sanota.numerod = SAITEMNOTA.numerod AND sanota.tipofac = SAITEMNOTA.tipofac AND sanota.numerof = 0)";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$data['fechai']);
        $sql->bindValue($i+=1,$data['fechaf']);
        if (!hash_equals('-', $data['edv']))
            $sql->bindValue($i+=1,$data['edv']);
        $sql->bindValue($i+=1,$tipo);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResumenFactura($data)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $edv = (!hash_equals('-', $data['edv'])) ? " AND codvend LIKE ?" : "";

        //QUERY
        $sql= "SELECT DISTINCT codvend, descto1, descto2, tasa, numerod, tipofac, fechae, codclie, descrip
                FROM SAFACT WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ?
                AND (tipofac = 'A' OR Tipofac = 'B')
                AND (Descto1 > 0 OR Descto2 > 0) $edv";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$data['fechai']);
        $sql->bindValue($i+=1,$data['fechaf']);
        if (!hash_equals('-', $data['edv']))
            $sql->bindValue($i+=1,$data['edv']);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResumenNotaDeEntrega($data)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $edv = (!hash_equals('-', $data['edv'])) ? " AND sanota.codvend LIKE ?" : "";

        //QUERY
        $sql= "SELECT DISTINCT
                sanota.codvend, 
                sanota.descuento,
                (SELECT tasa FROM SAFACT WHERE SAFACT.numerod = sanota.numerod AND SAFACT.tipofac = sanota.tipofac AND (sanota.tipofac = 'C' OR sanota.Tipofac = 'D')) 
                    AS tasa,
                sanota.numerod, 
                sanota.tipofac,
                sanota.fechae,
                sanota.codclie,
                sanota.rsocial AS descrip
                FROM sanota INNER JOIN SAFACT ON safact.numerod = sanota.numerod WHERE
                DATEADD(dd, 0, DATEDIFF(dd, 0, sanota.FechaE)) BETWEEN ? AND ? $edv
                AND (sanota.tipofac = 'C' OR sanota.Tipofac = 'D')
                AND sanota.descuento > 0";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$data['fechai']);
        $sql->bindValue($i+=1,$data['fechaf']);
        if (!hash_equals('-', $data['edv']))
            $sql->bindValue($i+=1,$data['edv']);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}

