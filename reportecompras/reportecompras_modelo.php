
<?php
//LLAMAMOS A LA CONEXION.
require_once("../acceso/conexion.php");

class ReporteCompras extends Conectar
{
    public function get_codprod_por_marca($marca)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $addQuery = (!hash_equals("-", $marca)) ? "AND prod.marca LIKE '%$marca%'" : "";

        $sql = "SELECT prod.Codprod AS codprod FROM SAPROD AS prod INNER JOIN saexis AS exis ON prod.CodProd = exis.CodProd
                    WHERE (activo = '1' AND exis.codubic = '01' $addQuery) OR (activo = '1' AND exis.codubic = '03' $addQuery) GROUP BY prod.CodProd";

        $sql = $conectar->prepare($sql);
        /*if(!hash_equals("-", $marca)){
            $sql->bindValue(1, $marca);
            $sql->bindValue(2, $marca);
        }*/
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_reportecompra_por_codprod($codprod, $fechai)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT prod.CodProd AS codproducto, prod.Descrip AS descrip, prod.CantEmpaq AS displaybultos, COALESCE(prod.CostAct / NULLIF(prod.CantEmpaq,0), 0) AS costodisplay,
                        (ultima.Costo * prod.Existen) AS costobultos,
                        COALESCE( ((prod.Precio1 - prod.CostAct)*100) / NULLIF(prod.Precio1,0), 0) AS rentabilidad,
                        penultima.FechaE AS fechapenultimacompra, penultima.Cantidad AS bultospenultimacompra,
                        ultima.FechaE AS fechaultimacompra, ultima.Cantidad AS bultosultimacompra,
                        semana1.cantidad AS semana1, semana2.cantidad AS semana2, semana3.cantidad AS semana3, semana4.cantidad AS semana4,
                        (semana1.cantidad + semana2.cantidad + semana3.cantidad + semana4.cantidad) AS totalventasmesanterior,
                        existencia.bultosexis AS bultosexistentes,
                        COALESCE((existencia.bultosexis / NULLIF((semana1.cantidad + semana2.cantidad + semana3.cantidad + semana4.cantidad), 0))*30, 0) AS diasdeinventario,
                        (semana1.cantidad + semana2.cantidad + semana3.cantidad + semana4.cantidad)*1.2 AS sugerido
                FROM SAPROD AS prod,
                    (SELECT top(1) tmp.*  FROM (SELECT top(2) item.* FROM SACOMP INNER JOIN SAITEMCOM AS item ON SACOMP.NumeroD = item.NumeroD  WHERE SACOMP.TipoCom = 'H' AND CodItem = ? AND numeroN IS NULL ORDER BY item.FechaE DESC) AS tmp ORDER BY FechaE ASC) AS penultima,
                    (SELECT TOP(1) item.* FROM SAITEMCOM AS item WHERE CodItem = ? AND tipocom = 'H' ORDER BY FechaE DESC) AS ultima,
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
        //penultima
        $sql->bindValue($i+=1, $codprod);
        //ultima
        $sql->bindValue($i+=1, $codprod);
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
}