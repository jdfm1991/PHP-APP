<?php
//LLAMAMOS A LA CONEXION.
require_once("../acceso/conexion.php");

class Kpi extends Conectar
{
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
                WHERE activo = '1' AND coordinador != '' AND savend_02.coordinador = ?
                ORDER BY savend.codvend";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $nombre);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_MaestroClientesPorRuta($ruta)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT codclie FROM saclie WHERE codvend = ? AND activo = '1'";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ruta);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_ClientesActivosPorRuta($ruta, $fechai, $fechaf)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT distinct(SAFACT.CodClie) AS CODCLIE FROM SAFACT WHERE SAFACT.CodVend = ? AND TipoFac in ('A') AND SAFACT.CodClie IN (SELECT SACLIE.CodClie FROM SACLIE INNER JOIN SACLIE_01 ON SACLIE.CodClie = SACLIE_01.CodClie
                WHERE ACTIVO = 1 AND (SACLIE.CodVend = ? or Ruta_Alternativa = ? OR Ruta_Alternativa_2 = ?)) AND DATEADD(dd, 0, DATEDIFF(dd, 0, SAFACT.FechaE)) between ? and ? AND NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac in ('A') AND x.NumeroR is not NULL AND cast(X.Monto as BIGINT) = cast((select Z.Monto from SAFACT AS Z where Z.NumeroD = x.NumeroR and Z.TipoFac in ('B'))as BIGINT))
                
                UNION
                
                SELECT distinct(SANOTA.CodClie) AS CODCLIE FROM SANOTA WHERE SANOTA.CodVend = ? AND TipoFac in ('C') AND SANOTA.numerof = '0' AND SANOTA.CodClie IN (SELECT SACLIE.CodClie FROM SACLIE INNER JOIN SACLIE_01 ON SACLIE.CodClie = SACLIE_01.CodClie
                WHERE ACTIVO = 1 AND (SACLIE.CodVend = ? or Ruta_Alternativa = ? OR Ruta_Alternativa_2 = ?)) AND DATEADD(dd, 0, DATEDIFF(dd, 0, SANOTA.FechaE)) between ? and ? AND NumeroD NOT IN (SELECT X.NumeroD FROM SANOTA AS X WHERE X.TipoFac in ('C') AND x.numerof is not NULL AND cast(X.subtotal as BIGINT) = cast((select Z.subtotal from SANOTA AS Z where Z.NumeroD = x.numerof and Z.TipoFac in ('D'))as BIGINT))";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);

        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_frecuenciaVisita($ruta)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT * FROM savend_02 WHERE CodVend = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ruta);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_ventasFactura($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT numerod FROM safact WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, safact.FechaE)) BETWEEN ? AND ? AND safact.codvend = ? AND tipofac = 'A' AND NumeroD NOT IN 
                (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'A' AND x.NumeroR IS NOT NULL AND
                CAST(X.Monto AS BIGINT) = CAST((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B') AS BIGINT))";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $ruta);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_ventasNotas($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT numerod FROM sanota WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, sanota.FechaE)) BETWEEN ? AND ? AND sanota.codvend = ? AND tipofac = 'C' AND SANOTA.numerof = '0' AND NumeroD NOT IN 
                (SELECT X.NumeroD FROM sanota AS X WHERE X.TipoFac = 'C' AND x.Numerof IS NOT NULL AND
                CAST(X.subtotal AS BIGINT) = CAST((SELECT Z.subtotal FROM SAnota AS Z WHERE Z.NumeroD = x.Numerof AND Z.TipoFac = 'D')AS BIGINT))";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $ruta);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_devolucionesFactura($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT numerod FROM safact WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, safact.FechaE)) BETWEEN ? AND ? AND safact.codvend = ? AND tipofac = 'B'";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $ruta);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_devolucionesNotas($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT numerod FROM SANOTA WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, SANOTA.FechaE)) BETWEEN ? AND ? AND SANOTA.codvend = ? AND tipofac = 'D'";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $ruta);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_montoDivisasDevolucionesFactura($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT COALESCE(SUM(TGravable/NULLIF(Tasa,0)), 0) AS MontoD FROM safact WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, safact.FechaE)) BETWEEN ? AND ? 
                AND safact.codvend = ? AND tipofac = 'B'";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $ruta);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_montoDivisasDevolucionesNotas($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT COALESCE(SUM(subtotal), 0) AS MontoD FROM sanota WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, sanota.FechaE)) BETWEEN ? AND ? 
                AND sanota.codvend = ? AND tipofac = 'D'";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $ruta);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }


}