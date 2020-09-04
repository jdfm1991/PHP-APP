
<?php
//LLAMAMOS A LA CONEXION.
require_once("../acceso/conexion.php");

class InventarioGlobal extends Conectar
{
	public function get_Almacenes()
	{

		$conectar = parent::conexion2();
		parent::set_names();

		$sql = "SELECT CodUbic AS codubi, Descrip AS descrip FROM sadepo ORDER BY codubic";
		$sql = $conectar->prepare($sql);
		$sql->execute();

		return $resultado = $sql->fetchAll();
	}

	public function getDevolucionesDeFactura($alm, $fechai, $fechaf)
	{

		//LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
		//CUANDO ES APPWEB ES CONEXION.
		$conectar = parent::conexion2();
		parent::set_names();
        //armamos una lista de los depositos, si no existe ninguno seleccionado no se considera para realizar la consulta
        $depo = "(" . substr($alm, 0, strlen($alm) - 1) . ")";
        if ($depo != "()") {
            $cond = "AND CodUbic IN " . $depo;
        } else {
            $cond = "";
        }

		//QUERY
		/*$calm = count($alm);
		$aux = "";
		if ($calm == 1) {
			$cond = "and CodUbic='$alm[0]'";
		} else if ($calm > 1) {
			for ($i = 1; $i < $calm; $i++)
				$aux .= " OR CodUbic='$alm[$i]'";
			$cond = "and (CodUbic='$alm[0]' $aux)";
		}*/
		$sql = "SELECT CodItem AS coditem, Cantidad AS cantidad, esunid AS esunid FROM SAITEMFAC WHERE NumeroD IN (SELECT fa.NumeroR FROM SAFACT AS fa WHERE TipoFac='A' " . $cond . " AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ?
                        AND (NumeroR IS NULL OR NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? GROUP BY x.NumeroR HAVING CAST(SUM(x.Monto) AS INT) < CAST(fa.Monto AS INT)))
                        AND NumeroD NOT IN (SELECT Despachos_Det.Numerod FROM APPWEBAJ.dbo.Despachos_Det))  AND NumeroD NOT IN (SELECT numerof FROM sanota)) AND tipofac = 'B'";

		//PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
        $sql->bindValue(1, $fechai);
        $sql->bindValue(2, $fechaf);
        $sql->bindValue(3, $fechai);
        $sql->bindValue(4, $fechaf);
		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
	}

    public function getInventarioGlobal($alm, $fechai, $fechaf)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //armamos una lista de los depositos, si no existe ninguno seleccionado no se considera para realizar la consulta
        $depo = "(" . substr($alm, 0, strlen($alm) - 1) . ")";
        if ($depo != "()") {
            $cond = "AND CodUbic IN " . $depo;
        } else {
            $cond = "";
        }

        /*$calm=count($alm);
        $aux = "";
        if($calm == 1 ) {
            $cond="and CodUbic='$alm[0]'";
        } else if($calm > 1) {
            for($i=1; $i<$calm; $i++)
                $aux .= " OR CodUbic='$alm[$i]'";
            $cond="and (CodUbic='$alm[0]' $aux)";
        }*/

        $sql = "SELECT CodProd, Descrip, CantEmpaq,
                    (SELECT isnull(SUM(cantidad),0)+isnull(0,0) FROM SAITEMFAC WHERE esunid='0' AND CodProd=CodItem ".$cond." AND numerod IN (SELECT fa.numerod FROM SAFACT AS fa WHERE TipoFac='A' AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? AND (NumeroR IS NULL OR NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? GROUP BY x.NumeroR HAVING CAST(SUM(x.Monto) AS INT)<CAST(fa.Monto AS INT))) AND NumeroD NOT IN (SELECT Despachos_Det.Numerod FROM APPWEBAJ.dbo.Despachos_Det))) AS bultosxdesp, 
                    (SELECT isnull(SUM(cantidad),0)+isnull(0,0) FROM SAITEMFAC WHERE esunid='1' AND CodProd=CodItem ".$cond." AND numerod IN (SELECT fa.numerod FROM SAFACT AS fa WHERE TipoFac='A' AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? AND (NumeroR IS NULL OR NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? GROUP BY x.NumeroR HAVING CAST(SUM(x.Monto) AS INT)<CAST(fa.Monto as INT))) AND NumeroD NOT IN (SELECT Despachos_Det.Numerod FROM APPWEBAJ.dbo.Despachos_Det))) AS paqxdesp,
                    
                    (SELECT isnull(SUM(exunidad),0)+isnull(0,0) FROM SAEXIS WHERE CodProd=SAPROD.CodProd ".$cond.") AS exunid, 
                    (SELECT isnull(SUM(existen),0)+isnull(0,0) FROM SAEXIS WHERE CodProd=SAPROD.CodProd ".$cond.") AS exis, 
                     
                    ((SELECT isnull(SUM(exunidad),0)+isnull(0,0) FROM SAEXIS WHERE CodProd=SAPROD.CodProd ".$cond.")+
                     (SELECT isnull(SUM(existen),0)+isnull(0,0) FROM SAEXIS WHERE CodProd=SAPROD.CodProd ".$cond.")+
                     (SELECT isnull(SUM(cantidad),0)+isnull(0,0) FROM SAITEMFAC WHERE esunid='0' AND CodProd=CodItem ".$cond." AND numerod IN (SELECT fa.numerod FROM SAFACT AS fa WHERE TipoFac='A' AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? AND (NumeroR IS NULL OR NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? GROUP BY x.NumeroR HAVING CAST(SUM(x.Monto) AS INT)<CAST(fa.Monto AS INT))) AND NumeroD NOT IN 
                    (SELECT numeros FROM appfacturas_det))) + 
                     (SELECT isnull(SUM(cantidad),0)+isnull(0,0) FROM SAITEMFAC WHERE esunid='1' AND CodProd=CodItem ".$cond." AND numerod IN (SELECT fa.numerod FROM SAFACT AS fa WHERE TipoFac='A' AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? AND (NumeroR IS NULL OR NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? GROUP BY x.NumeroR HAVING CAST(SUM(x.Monto) AS INT)<CAST(fa.Monto AS INT))) AND NumeroD NOT IN 
                     (SELECT numeros FROM appfacturas_det)))) AS tt
                     
                      FROM SAPROD
                     WHERE CantEmpaq>0 GROUP BY CodProd, Descrip, CantEmpaq HAVING  
                     ((SELECT isnull(SUM(exunidad),0)+isnull(0,0) FROM SAEXIS WHERE CodProd=SAPROD.CodProd ".$cond.")+
                     (SELECT isnull(SUM(existen),0)+isnull(0,0) FROM SAEXIS WHERE CodProd=SAPROD.CodProd ".$cond.")+
                     (SELECT isnull(SUM(cantidad),0)+isnull(0,0) FROM SAITEMFAC WHERE esunid='0' AND CodProd=CodItem ".$cond." AND numerod IN (SELECT fa.numerod FROM SAFACT AS fa WHERE TipoFac='A' AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? AND (NumeroR IS NULL OR NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? GROUP BY x.NumeroR HAVING CAST(SUM(x.Monto) AS INT)<CAST(fa.Monto AS INT))) AND NumeroD NOT IN (SELECT Despachos_Det.Numerod FROM APPWEBAJ.dbo.Despachos_Det))) + 
                     (SELECT isnull(SUM(cantidad),0)+isnull(0,0) FROM SAITEMFAC WHERE esunid='1' AND CodProd=CodItem ".$cond." AND numerod IN (SELECT fa.numerod FROM SAFACT AS fa WHERE TipoFac='A' AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? AND (NumeroR IS NULL OR NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? GROUP BY x.NumeroR HAVING CAST(SUM(x.Monto) AS INT)<CAST(fa.Monto AS INT))) AND NumeroD NOT IN (SELECT Despachos_Det.Numerod FROM APPWEBAJ.dbo.Despachos_Det)))) > 0  ORDER BY CodProd";
        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        for($i=1; $i <=22; $i+=2){
            $sql->bindValue($i, $fechai);
            $sql->bindValue($i+1, $fechaf);
        }
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
