<?php


class KpiMarcas extends Conectar {

    public static function todos($orden = 'ASC')
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.


        $sql= "SELECT id, descripcion, fechae FROM Kpi_marcas ORDER BY id $orden";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function bultosActivadosPorMarca($ruta, $marca, $fechai, $fechaf)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT DISTINCT(CodClie) FROM saitemfac INNER JOIN saprod ON saitemfac.coditem = saprod.codprod INNER JOIN
                SAFACT ON SAITEMFAC.NumeroD = SAFACT.NumeroD WHERE
                DATEADD(dd, 0, DATEDIFF(dd, 0, saitemfac.FechaE)) BETWEEN ? AND ? AND saprod.marca LIKE ? AND
                SAITEMFAC.codvend = ? AND saitemfac.tipofac = 'A' AND SAFACT.tipofac = 'A' AND SAFACT.NumeroD NOT IN
                (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'A' AND x.NumeroR IS NOT NULL AND
                CAST(X.Monto AS BIGINT) = CAST((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B') AS BIGINT))
                
                UNION
                
                SELECT DISTINCT(CodClie) FROM saitemnota INNER JOIN saprod ON saitemnota.coditem = saprod.codprod INNER JOIN
                sanota ON saitemnota.NumeroD = sanota.NumeroD WHERE
                DATEADD(dd, 0, DATEDIFF(dd, 0, saitemnota.FechaE)) BETWEEN ? AND ? AND saprod.marca LIKE ? AND
                saitemnota.codvend = ? AND saitemnota.tipofac = 'C' AND sanota.tipofac = 'C' AND numerof = '0' AND sanota.NumeroD NOT IN
                (SELECT X.NumeroD FROM sanota AS X WHERE X.TipoFac = 'C' AND x.Numerof IS NOT NULL AND
                CAST(X.subtotal AS BIGINT) = CAST((SELECT Z.subtotal FROM sanota AS Z WHERE Z.NumeroD = x.Numerof AND Z.TipoFac = 'D') AS BIGINT))";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue($i+=1, $fechai);
        $result->bindValue($i+=1, $fechaf);
        $result->bindValue($i+=1, $marca);
        $result->bindValue($i+=1, $ruta);

        $result->bindValue($i+=1, $fechai);
        $result->bindValue($i+=1, $fechaf);
        $result->bindValue($i+=1, $marca);
        $result->bindValue($i+=1, $ruta);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}