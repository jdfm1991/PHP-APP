<?php


class KpiLogro extends Conectar {

    public static function Kg_fact($ruta, $fechai, $fechaf, $tipoFac)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "select sum(CASE saitemfac.Esunid WHEN 1 then (cantidad/cantempaq)*saprod.tara ELSE cantidad*saprod.tara END) AS kg
			 from saitemfac inner join saprod on saitemfac.coditem = saprod.codprod where 
			 DATEADD(dd, 0, DATEDIFF(dd, 0, saitemfac.FechaE)) between ? and ? and codvend = ? and (tipofac = ?)";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue($i+=1, $fechai);
        $result->bindValue($i+=1, $fechaf);
        $result->bindValue($i+=1, $ruta);
        $result->bindValue($i+=1, $tipoFac);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function Kg_nota($ruta, $fechai, $fechaf, $tipoFac)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "select sum(CASE saitemnota.esunidad WHEN 1 then (cantidad/cantempaq)*saprod.tara ELSE cantidad*saprod.tara END) AS kg
			 from saitemnota inner join saprod on saitemnota.coditem = saprod.codprod where 
			 DATEADD(dd, 0, DATEDIFF(dd, 0, saitemnota.FechaE)) between ? and ? and codvend = ? and (tipofac = ?)";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue($i+=1, $fechai);
        $result->bindValue($i+=1, $fechaf);
        $result->bindValue($i+=1, $ruta);
        $result->bindValue($i+=1, $tipoFac);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function Unid_fact($ruta, $fechai, $fechaf, $tipoFac)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "select SUM((CASE saitemfac.Esunid WHEN 1 then cantidad ELSE cantidad*cantempaq END)) AS paq
			 from saitemfac inner join saprod on saitemfac.coditem = saprod.codprod where 
			 DATEADD(dd, 0, DATEDIFF(dd, 0, saitemfac.FechaE)) between ? and ? and codvend = ? and (tipofac = ?)";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue($i+=1, $fechai);
        $result->bindValue($i+=1, $fechaf);
        $result->bindValue($i+=1, $ruta);
        $result->bindValue($i+=1, $tipoFac);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function Unid_nota($ruta, $fechai, $fechaf, $tipoFac)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "select SUM((CASE saitemnota.esunidad WHEN 1 then cantidad ELSE cantidad*cantempaq END)) AS paq
                from saitemnota inner join saprod on saitemnota.coditem = saprod.codprod where 
                DATEADD(dd, 0, DATEDIFF(dd, 0, saitemnota.FechaE)) between ? and ? and codvend = ? and (tipofac = ?)";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue($i+=1, $fechai);
        $result->bindValue($i+=1, $fechaf);
        $result->bindValue($i+=1, $ruta);
        $result->bindValue($i+=1, $tipoFac);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function Bul_fact($ruta, $fechai, $fechaf, $tipoFac)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "select sum((CASE saitemfac.Esunid WHEN 1 then cantidad/cantempaq ELSE cantidad END)) AS bul
			 from saitemfac inner join saprod on saitemfac.coditem = saprod.codprod where 
			 DATEADD(dd, 0, DATEDIFF(dd, 0, saitemfac.FechaE)) between ? and ? and codvend = ? and (tipofac = ?)";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue($i+=1, $fechai);
        $result->bindValue($i+=1, $fechaf);
        $result->bindValue($i+=1, $ruta);
        $result->bindValue($i+=1, $tipoFac);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function Bul_nota($ruta, $fechai, $fechaf, $tipoFac)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "select sum((CASE saitemnota.esunidad WHEN 1 then cantidad/cantempaq ELSE cantidad END)) AS bul
			 from saitemnota inner join saprod on saitemnota.coditem = saprod.codprod where 
			 DATEADD(dd, 0, DATEDIFF(dd, 0, saitemnota.FechaE)) between ? and ? and codvend = ? and (tipofac = ?)";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue($i+=1, $fechai);
        $result->bindValue($i+=1, $fechaf);
        $result->bindValue($i+=1, $ruta);
        $result->bindValue($i+=1, $tipoFac);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}