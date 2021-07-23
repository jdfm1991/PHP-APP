
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

        $marca = (!hash_equals('-', $data['marca'])) ? " AND  SAITEMFAC.codvend LIKE ?" : "";
        $edv = (!hash_equals('-', $data['edv'])) ? " AND saprod.marca LIKE ?" : "";

        //QUERY
        $sql= "SELECT
                (SELECT codvend FROM savend WHERE savend.codvend = SAITEMFAC.codvend) AS codvend,
                (SELECT descrip FROM savend WHERE savend.codvend = SAITEMFAC.codvend) AS vendedor,
                (SELECT clase FROM savend WHERE savend.codvend = SAITEMFAC.codvend) AS clasevend,
                SAITEMFAC.tipofac AS tipo,
                SAITEMFAC.numerod AS numerod,
                (SELECT codclie FROM SAFACT WHERE SAFACT.numerod = SAITEMFAC.numerod AND SAFACT.tipofac = SAITEMFAC.tipofac) AS codclie,
                (SELECT Descrip FROM SAFACT WHERE SAFACT.numerod = SAITEMFAC.numerod AND SAFACT.tipofac = SAITEMFAC.tipofac) AS cliente,
                (SELECT saclie_01.codnestle FROM SAFACT INNER JOIN saclie_01 ON SAFACT.codclie = saclie_01.codclie WHERE SAFACT.numerod = SAITEMFAC.numerod AND SAFACT.tipofac = SAITEMFAC.tipofac) AS codnestle,
                (SELECT saclie_01.clasificacion FROM SAFACT INNER JOIN saclie_01 ON SAFACT.codclie = saclie_01.codclie WHERE SAFACT.numerod = SAITEMFAC.numerod AND SAFACT.tipofac = SAITEMFAC.tipofac) AS clasificacion,
                SAITEMFAC.coditem,
                SAITEMFAC.Descrip1 AS descripcion,
                (SELECT marca FROM SAPROD WHERE SAITEMFAC.coditem = SAPROD.CodProd) AS marca,
                SAITEMFAC.cantidad,
                (CASE SAITEMFAC.EsUnid WHEN 1 then 'PAQ' ELSE 'BULT' END) AS unid,
                (CASE SAITEMFAC.EsUnid WHEN 1 then cantidad ELSE cantidad*cantempaq END) AS paq,
                (CASE SAITEMFAC.EsUnid WHEN 1 then cantidad/cantempaq ELSE cantidad END) AS bul,
                (SELECT descrip FROM sainsta WHERE sainsta.codinst = saprod.codinst) AS instancia,
                SAITEMFAC.TotalItem AS montod,
                SAITEMFAC.Descto AS descuento,
                (SELECT tasa FROM SAFACT WHERE SAFACT.numerod = SAITEMFAC.numerod AND SAFACT.tipofac = SAITEMFAC.tipofac) AS factor,
                SAITEMFAC.fechae,
                SAITEMFAC.EsExento,
                (CASE SAITEMFAC.EsUnid WHEN 1  then (cantidad/cantempaq)*saprod.tara ELSE cantidad*saprod.tara END) AS kg,
                CONCAT('(',MONTH(SAITEMFAC.fechae),')','-',UPPER(DATENAME(MONTH,SAITEMFAC.fechae)),'-(',YEAR(SAITEMFAC.fechae),')') MES
                FROM SAITEMFAC INNER JOIN saprod ON SAITEMFAC.coditem = saprod.codprod WHERE
                DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMFAC.FechaE)) between ? AND ? $marca $edv AND (SAITEMFAC.tipofac = 'A' OR SAITEMFAC.Tipofac = 'B')  ORDER BY SAITEMFAC.fechae";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$data['fechai']);
        $sql->bindValue($i+=1,$data['fechaf']);
        if (!hash_equals('-', $data['marca']))
            $sql->bindValue($i+=1,$data['marca']);
        if (!hash_equals('-', $data['edv']))
            $sql->bindValue($i+=1,$data['edv']);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTabladinamicaNotaDeEntrega($data)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $marca = (!hash_equals('-', $data['marca'])) ? " AND saprod.marca LIKE ?" : "";
        $edv = (!hash_equals('-', $data['edv'])) ? " AND  sanota.codvend LIKE ?" : "";

        //QUERY
        $sql= "SELECT
                (SELECT codvend FROM savend WHERE savend.codvend = SAITEMNOTA.codvend) AS codvend,
                (SELECT descrip FROM savend WHERE savend.codvend = SAITEMNOTA.codvend) AS vendedor,
                (SELECT clase FROM savend WHERE savend.codvend = SAITEMNOTA.codvend) AS clasevend,
                saitemnota.tipofac AS tipo,
                saitemnota.numerod AS numerod,
                (SELECT codclie FROM SANOTA WHERE SANOTA.numerod = SAITEMNOTA.numerod AND SANOTA.tipofac = SAITEMNOTA.tipofac) AS codclie,
                (SELECT rsocial FROM SANOTA WHERE SANOTA.numerod = SAITEMNOTA.numerod AND SANOTA.tipofac = SAITEMNOTA.tipofac) AS cliente,
                (SELECT saclie_01.codnestle FROM SANOTA INNER JOIN saclie_01 ON SANOTA.codclie = saclie_01.codclie WHERE SANOTA.numerod = SAITEMNOTA.numerod AND SANOTA.tipofac = SAITEMNOTA.tipofac) AS codnestle,
                (SELECT saclie_01.clasificacion FROM SANOTA INNER JOIN saclie_01 ON SANOTA.codclie = saclie_01.codclie WHERE SANOTA.numerod = SAITEMNOTA.numerod AND SANOTA.tipofac = SAITEMNOTA.tipofac) AS clasificacion,
                SAITEMNOTA.coditem,
                SAITEMNOTA.descripcion,
                (SELECT marca FROM SAPROD WHERE SAITEMNOTA.coditem = SAPROD.CodProd) AS marca,
                SAITEMNOTA.cantidad,
                (CASE SAITEMNOTA.esunidad WHEN 1 then 'PAQ' ELSE 'BULT' END) AS unid,
                (CASE SAITEMNOTA.esunidad WHEN 1 then cantidad ELSE cantidad*cantempaq END) AS paq,
                (CASE SAITEMNOTA.esunidad WHEN 1 then cantidad/cantempaq ELSE cantidad END) AS bul,
                (SELECT descrip FROM sainsta WHERE sainsta.codinst = saprod.codinst) AS instancia,
                (CASE SAITEMNOTA.esexento WHEN 1  then SAITEMNOTA.total ELSE SAITEMNOTA.total / 1.16 END) AS montod,
                --SAITEMNOTA.total AS montod,
                (CASE SAITEMNOTA.esexento WHEN 1  then SAITEMNOTA.descuento ELSE SAITEMNOTA.descuento / 1.16 END) AS descuento,
                --SAITEMNOTA.descuento AS descuento,
                (SELECT tasa FROM SAFACT WHERE SAFACT.numerod = SAITEMNOTA.numerod AND SAFACT.tipofac = SAITEMNOTA.tipofac) AS factor,
                SAITEMNOTA.fechae,
                SAITEMNOTA.esexento,
                (CASE SAITEMNOTA.esunidad WHEN 1  then (cantidad/cantempaq)*saprod.tara ELSE cantidad*saprod.tara END) AS kg,
                CONCAT('(',MONTH(saitemnota.fechae),')','-',UPPER(DATENAME(MONTH,saitemnota.fechae)),'-(',YEAR(saitemnota.fechae),')') MES
                 FROM SAITEMNOTA INNER JOIN saprod ON SAITEMNOTA.coditem = saprod.codprod
                 INNER JOIN sanota ON saitemnota.numerod = sanota.numerod AND saitemnota.tipofac = sanota.tipofac WHERE
                 DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMNOTA.FechaE)) between ? AND ? $marca $edv  AND (SAITEMNOTA.tipofac = 'C' OR SAITEMNOTA.Tipofac = 'D') AND  
                SANOTA.numerof =(SELECT numerof FROM sanota WHERE sanota.numerod = SAITEMNOTA.numerod AND sanota.tipofac = SAITEMNOTA.tipofac AND sanota.numerof = 0) ORDER BY SAITEMNOTA.fechae";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$data['fechai']);
        $sql->bindValue($i+=1,$data['fechaf']);
        if (!hash_equals('-', $data['marca']))
            $sql->bindValue($i+=1,$data['marca']);
        if (!hash_equals('-', $data['edv']))
            $sql->bindValue($i+=1,$data['edv']);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRetencionesOtrosPeriodos($fechai, $fechaf)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql= "SELECT retencioniva, fechaemision, rifcliente, nombre, tipodoc, numerodoc, tiporeg, factafectada, fecharetencion, totalgravable_contribuye, totalivacontribuye
                FROM DBO.VW_ADM_LIBROIVAVENTAS
                WHERE ( ? <=FECHAEMISION) AND (FECHAEMISION<= ? ) AND (NOT(FECHARETENCION IS NULL)
                    AND NOT(( ? <=FECHARETENCION) AND (FECHARETENCION<= ? ))) AND TIPO='81'
                ORDER BY FECHAT";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$fechai);
        $sql->bindValue($i+=1,$fechaf);
        $sql->bindValue($i+=1,$fechai);
        $sql->bindValue($i+=1,$fechaf);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getRetencionItem($fechai, $fechaf, $numerodoc)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql= "SELECT retencioniva, nroretencion, retencioniva
                FROM DBO.VW_ADM_LIBROIVAVENTAS
                WHERE ( ? <=FECHAEMISION) AND (FECHAEMISION<= ? ) AND ((FECHARETENCION IS NULL)
                    OR (( ? <=FECHARETENCION) AND (FECHARETENCION<= ? ))) AND factafectada = ? AND tipodoc = 'RET'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$fechai);
        $sql->bindValue($i+=1,$fechaf);
        $sql->bindValue($i+=1,$fechai);
        $sql->bindValue($i+=1,$fechaf);
        $sql->bindValue($i+=1,$numerodoc);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }
}

