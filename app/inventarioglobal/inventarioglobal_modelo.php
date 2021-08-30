<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class InventarioGlobal extends Conectar
{
    public function getInventarioGlobal($fechai, $fechaf, $alm=array())
    {
        $i = 0;
        $cond = $depo = "";
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        if (count($alm) > 0) {
            $aux = "";
            //se contruye un string para listar los depositvos seleccionados
            //en caso que no haya ninguno, sera vacio
            foreach ($alm as $num)
                $aux .= " OR CodUbic = ?";

            //armamos una lista de los depositos, si no existe ninguno seleccionado no se considera para realizar la consulta
            $depo = "(" . substr($aux, 4, strlen($aux)) . ")";

            $cond = ($depo != "()")
                ? ("AND ".$depo)
                : "";
        }

        $sql = "SELECT CodProd, Descrip, CantEmpaq,
                    (SELECT isnull(SUM(cantidad),0)+isnull(0,0) FROM SAITEMFAC WHERE esunid= '0' AND CodProd=CodItem ".$cond." AND numerod IN (SELECT fa.numerod FROM SAFACT AS fa WHERE TipoFac= 'A' AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? AND (NumeroR IS NULL OR NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? GROUP BY x.NumeroR HAVING CAST(SUM(x.Monto) AS INT)<CAST(fa.Monto AS INT))) AND NumeroD NOT IN (SELECT Despachos_Det.Numerod FROM APPWEBAJ.dbo.Despachos_Det))) AS bultosxdesp, 
                    (SELECT isnull(SUM(cantidad),0)+isnull(0,0) FROM SAITEMFAC WHERE esunid= '1' AND CodProd=CodItem ".$cond." AND numerod IN (SELECT fa.numerod FROM SAFACT AS fa WHERE TipoFac= 'A' AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? AND (NumeroR IS NULL OR NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? GROUP BY x.NumeroR HAVING CAST(SUM(x.Monto) AS INT)<CAST(fa.Monto as INT))) AND NumeroD NOT IN (SELECT Despachos_Det.Numerod FROM APPWEBAJ.dbo.Despachos_Det))) AS paqxdesp,
                    
                    (SELECT isnull(SUM(exunidad),0)+isnull(0,0) FROM SAEXIS WHERE CodProd=SAPROD.CodProd ".$cond.") AS exunid, 
                    (SELECT isnull(SUM(existen),0)+isnull(0,0) FROM SAEXIS WHERE CodProd=SAPROD.CodProd ".$cond.") AS exis, 
                     
                    ((SELECT isnull(SUM(exunidad),0)+isnull(0,0) FROM SAEXIS WHERE CodProd=SAPROD.CodProd ".$cond.")+
                     (SELECT isnull(SUM(existen),0)+isnull(0,0) FROM SAEXIS WHERE CodProd=SAPROD.CodProd ".$cond.")+
                     (SELECT isnull(SUM(cantidad),0)+isnull(0,0) FROM SAITEMFAC WHERE esunid= '0' AND CodProd=CodItem ".$cond." AND numerod IN (SELECT fa.numerod FROM SAFACT AS fa WHERE TipoFac= 'A' AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? AND (NumeroR IS NULL OR NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? GROUP BY x.NumeroR HAVING CAST(SUM(x.Monto) AS INT)<CAST(fa.Monto AS INT))) AND NumeroD NOT IN 
                    (SELECT numeros FROM appfacturas_det))) + 
                     (SELECT isnull(SUM(cantidad),0)+isnull(0,0) FROM SAITEMFAC WHERE esunid= '1' AND CodProd=CodItem ".$cond." AND numerod IN (SELECT fa.numerod FROM SAFACT AS fa WHERE TipoFac= 'A' AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? AND (NumeroR IS NULL OR NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? GROUP BY x.NumeroR HAVING CAST(SUM(x.Monto) AS INT)<CAST(fa.Monto AS INT))) AND NumeroD NOT IN 
                     (SELECT numeros FROM appfacturas_det)))) AS tt
                     
                      FROM SAPROD
                     WHERE CantEmpaq>0 GROUP BY CodProd, Descrip, CantEmpaq HAVING  
                     ((SELECT isnull(SUM(exunidad),0)+isnull(0,0) FROM SAEXIS WHERE CodProd=SAPROD.CodProd ".$cond.")+
                     (SELECT isnull(SUM(existen),0)+isnull(0,0) FROM SAEXIS WHERE CodProd=SAPROD.CodProd ".$cond.")+
                     (SELECT isnull(SUM(cantidad),0)+isnull(0,0) FROM SAITEMFAC WHERE esunid= '0' AND CodProd=CodItem ".$cond." AND numerod IN (SELECT fa.numerod FROM SAFACT AS fa WHERE TipoFac= 'A' AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? AND (NumeroR IS NULL OR NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? GROUP BY x.NumeroR HAVING CAST(SUM(x.Monto) AS INT)<CAST(fa.Monto AS INT))) AND NumeroD NOT IN (SELECT Despachos_Det.Numerod FROM APPWEBAJ.dbo.Despachos_Det))) + 
                     (SELECT isnull(SUM(cantidad),0)+isnull(0,0) FROM SAITEMFAC WHERE esunid= '1' AND CodProd=CodItem ".$cond." AND numerod IN (SELECT fa.numerod FROM SAFACT AS fa WHERE TipoFac= 'A' AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? AND (NumeroR IS NULL OR NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? GROUP BY x.NumeroR HAVING CAST(SUM(x.Monto) AS INT)<CAST(fa.Monto AS INT))) AND NumeroD NOT IN (SELECT Despachos_Det.Numerod FROM APPWEBAJ.dbo.Despachos_Det)))) > 0  ORDER BY CodProd";
        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        if ($depo != "()") {
            foreach ($alm AS $num)
                $sql->bindValue($i+=1, $num);
        }
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);

        if ($depo != "()") {
            foreach ($alm AS $num)
                $sql->bindValue($i+=1, $num);
        }
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);

        if ($depo != "()") {
            for($aux=1; $aux<=4; $aux++) {
                foreach ($alm as $num)
                    $sql->bindValue($i += 1, $num);
            }
        }

        if ($depo != "()") {
            foreach ($alm AS $num)
                $sql->bindValue($i+=1, $num);
        }
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);

        if ($depo != "()") {
            foreach ($alm AS $num)
                $sql->bindValue($i+=1, $num);
        }
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);

        if ($depo != "()") {
            for($aux=1; $aux<=2; $aux++) {
                foreach ($alm as $num)
                    $sql->bindValue($i += 1, $num);
            }
        }

        if ($depo != "()") {
            foreach ($alm AS $num)
                $sql->bindValue($i+=1, $num);
        }
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);

        if ($depo != "()") {
            foreach ($alm AS $num)
                $sql->bindValue($i+=1, $num);
        }
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);

        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
