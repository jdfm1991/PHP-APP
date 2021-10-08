
<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class ReporteCompras extends Conectar
{
    public function get_codprod_por_marca($marca)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $addQuery = (!hash_equals("-", $marca))
            ? "AND prod.marca LIKE '%$marca%'"
            : "";

        $sql = "SELECT prod.Codprod AS codprod 
                FROM SAPROD AS prod 
                    INNER JOIN saexis AS exis ON prod.CodProd = exis.CodProd
                WHERE (activo = '1' AND exis.codubic = '01' $addQuery) OR (activo = '1' AND exis.codubic = '03' $addQuery) 
                GROUP BY prod.CodProd";

        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_datos_producto($codprod)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT prod.codprod, prod.descrip, prod.CantEmpaq displaybultos, 
                       prod.fechauc, prod.precio1, prod.CostAct costoactual
                FROM SAPROD AS prod WHERE prod.CodProd = ?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codprod);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_costos($codprod)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT TOP(1) item.FechaE, prod2.costo, prod.CantEmpaq,
                     COALESCE(prod2.costo/NULLIF(CantEmpaq,0), 0) costodisplay, (prod2.costo) costobultos
                FROM SAPROD AS prod
                    INNER JOIN SAPROD_02 AS prod2 ON prod2.CodProd = prod.CodProd
                    INNER JOIN SAITEMCOM AS item ON prod.CodProd = item.CodItem
                WHERE item.coditem = ? AND item.TipoCom IN ('H','J') ORDER BY FechaE DESC";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codprod);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_ultimas_compras($codprod)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT TOP(2) item.fechae, item.NumeroD, SUM(item.Cantidad) cantBult
                FROM SACOMP AS comp
                    INNER JOIN SAITEMCOM AS item ON comp.NumeroD = item.NumeroD
                    INNER JOIN SAPROD AS prod ON item.CodItem = prod.CodProd
                WHERE item.TipoCom = 'H' AND prod.CodProd = ? AND comp.numeroN IS NULL
                GROUP BY item.NumeroD ,item.FechaE
                ORDER BY item.FechaE DESC";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codprod);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_ventas_mes_anterior($codprod, $fechai, $fechaf) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT fechae, COALESCE((CASE WHEN EsUnid=1 THEN cantidad/CantEmpaq ELSE Cantidad END), 0) AS cantidadBult
                FROM SAITEMFAC INNER JOIN SAPROD prod ON prod.CodProd = saitemfac.CodItem
                WHERE CodItem = ? AND DATEADD(dd, 0, DATEDIFF(dd, 0, fechaE)) between ? and ? AND TipoFac = 'A'
                UNION
                SELECT fechae, COALESCE((CASE WHEN esunidad=1 THEN cantidad/CantEmpaq ELSE Cantidad END), 0) AS cantidadBult
                FROM SAITEMNOTA INNER JOIN SAPROD prod ON prod.CodProd = saitemnota.CodItem
                WHERE CodItem = ? AND DATEADD(dd, 0, DATEDIFF(dd, 0, fechaE)) between ? and ? AND TipoFac = 'C'";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $codprod);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);

        $sql->bindValue($i+=1, $codprod);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_bultos_existentes($codprod)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT sum(existen) bultosexis FROM SAEXIS WHERE CodProd = ?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codprod);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_productos_no_vendidos($codprod, $fechai, $fechaf) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT COALESCE(SUM((CASE WHEN EsUnid=1 THEN Cantidad/CantEmpaq ELSE Cantidad END)), 0) AS cantidadBult
                FROM SAITEMFAC item
                    INNER JOIN SAPROD prod ON prod.CodProd = item.CodItem
                WHERE CodItem = ? AND item.FechaE BETWEEN ? AND ? AND TipoFac = 'F' AND OTipo IS NULL";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $codprod);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /*public function get_reportecompra_por_codprod($codprod, $fechai)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT prod.CodProd AS codproducto, prod.Descrip AS descrip, prod.CantEmpaq AS displaybultos, COALESCE(prod.CostAct / NULLIF(prod.CantEmpaq,0), 0) AS costodisplay,
                        (ISNULL((SELECT TOP(1) saitemcom.Costo FROM saitemcom WHERE CodItem = prod.CodProd AND tipocom = 'H' ORDER BY FechaE DESC), 0) * prod.Existen) AS costobultos,
                        COALESCE( ((prod.Precio1 - prod.CostAct)*100) / NULLIF(prod.Precio1,0), 0) AS rentabilidad,
                        semana1.cantidad AS semana1, semana2.cantidad AS semana2, semana3.cantidad AS semana3, semana4.cantidad AS semana4,
                        existencia.bultosexis AS bultosexistentes,
                        COALESCE((existencia.bultosexis / NULLIF((semana1.cantidad + semana2.cantidad + semana3.cantidad + semana4.cantidad), 0))*30, 0) AS diasdeinventario
                FROM SAPROD AS prod,
                    (SELECT COALESCE(sum(tmp.cantidad), 0) AS cantidad FROM (SELECT Cantidad, FechaE FROM SAITEMFAC WHERE CodItem = ? AND FechaE BETWEEN DATEADD(mm,-1,DATEADD(mm,DATEDIFF(mm,0, ?),0)) AND DATEADD(ms,-3,DATEADD(mm,0,DATEADD(mm,DATEDIFF(mm,0, ?),0))) GROUP BY FechaE, Cantidad HAVING
                    (DATEDIFF(dd, DATEADD(mm,-1,DATEADD(mm,DATEDIFF(mm,0, ?),0)), DATEADD(dd, 1, DATEDIFF(dd, 0, FechaE))) >= 1
                     AND DATEDIFF(dd, DATEADD(mm,-1,DATEADD(mm,DATEDIFF(mm,0, ?),0)), DATEADD(dd, 1, DATEDIFF(dd, 0, FechaE))) <= 7)) AS tmp)
                     AS semana1,
                     (SELECT COALESCE(sum(tmp.cantidad), 0) AS cantidad FROM (SELECT Cantidad, FechaE FROM SAITEMFAC WHERE CodItem = ? AND FechaE BETWEEN DATEADD(mm,-1,DATEADD(mm,DATEDIFF(mm,0, ?),0)) AND DATEADD(ms,-3,DATEADD(mm,0,DATEADD(mm,DATEDIFF(mm,0, ?),0))) GROUP BY FechaE, Cantidad HAVING
                     (DATEDIFF(dd, DATEADD(mm,-1,DATEADD(mm,DATEDIFF(mm,0, ?),0)), DATEADD(dd, 1, DATEDIFF(dd, 0, FechaE))) >= 8
                      AND DATEDIFF(dd, DATEADD(mm,-1,DATEADD(mm,DATEDIFF(mm,0, ?),0)), DATEADD(dd, 1, DATEDIFF(dd, 0, FechaE))) <= 14)) AS tmp)
                      AS semana2,
                     (SELECT COALESCE(sum(tmp.cantidad), 0) AS cantidad FROM (SELECT Cantidad, FechaE FROM SAITEMFAC WHERE CodItem = ? AND FechaE BETWEEN DATEADD(mm,-1,DATEADD(mm,DATEDIFF(mm,0, ?),0)) AND DATEADD(ms,-3,DATEADD(mm,0,DATEADD(mm,DATEDIFF(mm,0, ?),0))) GROUP BY FechaE, Cantidad HAVING
                     (DATEDIFF(dd, DATEADD(mm,-1,DATEADD(mm,DATEDIFF(mm,0, ?),0)), DATEADD(dd, 1, DATEDIFF(dd, 0, FechaE))) >= 15
                      AND DATEDIFF(dd, DATEADD(mm,-1,DATEADD(mm,DATEDIFF(mm,0, ?),0)), DATEADD(dd, 1, DATEDIFF(dd, 0, FechaE))) <= 21)) AS tmp)
                      AS semana3,
                     (SELECT COALESCE(sum(tmp.cantidad), 0) AS cantidad FROM (SELECT Cantidad, FechaE FROM SAITEMFAC WHERE CodItem = ? AND FechaE BETWEEN DATEADD(mm,-1,DATEADD(mm,DATEDIFF(mm,0, ?),0)) AND DATEADD(ms,-3,DATEADD(mm,0,DATEADD(mm,DATEDIFF(mm,0, ?),0))) GROUP BY FechaE, Cantidad HAVING
                     (DATEDIFF(dd, DATEADD(mm,-1,DATEADD(mm,DATEDIFF(mm,0, ?),0)), DATEADD(dd, 1, DATEDIFF(dd, 0, FechaE))) >= 22)) AS tmp)
                      AS semana4,
                     (SELECT SUM(existen) AS bultosexis FROM SAEXIS WHERE CodProd = ?) AS existencia
                WHERE CodProd = ?";

        $sql = $conectar->prepare($sql);
        //semana1
        $sql->bindValue($i+=1, $codprod);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechai);
        //semana2
        $sql->bindValue($i+=1, $codprod);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechai);
        //semana3
        $sql->bindValue($i+=1, $codprod);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechai);
        //semana4
        $sql->bindValue($i+=1, $codprod);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechai);
        //existencia
        $sql->bindValue($i+=1, $codprod);
        //codprod
        $sql->bindValue($i+=1, $codprod);
        $sql->execute();

        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_ultimascompras_por_codprod($codprod)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT penultimacompra.FechaE AS fechapenultimacompra, 
                       penultimacompra.Cantidad AS bultospenultimacompra,
                       ultimacompra.FechaE AS fechaultimacompra, 
                       ultimacompra.Cantidad AS bultosultimacompra
                FROM 
                    (SELECT top(1) tmp.*  FROM (SELECT top(2) item.* FROM SACOMP INNER JOIN SAITEMCOM AS item ON SACOMP.NumeroD = item.NumeroD  WHERE SACOMP.TipoCom = 'H' AND CodItem = ? AND numeroN IS NULL ORDER BY item.FechaE DESC) AS tmp ORDER BY FechaE ASC) AS penultimacompra,
                    (SELECT TOP(1) item.* FROM SAITEMCOM AS item WHERE CodItem = ? AND tipocom = 'H' ORDER BY FechaE DESC) AS ultimacompra";

        $sql = $conectar->prepare($sql);
        //penultima
        $sql->bindValue($i+=1, $codprod);
        //ultima
        $sql->bindValue($i+=1, $codprod);
        $sql->execute();

        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }*/
}