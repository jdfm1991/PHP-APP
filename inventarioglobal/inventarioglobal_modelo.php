
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

		//QUERY
		$calm = count($alm);
		$aux = "";
		if ($calm == 1) {
			$cond = "and CodUbic='$alm[0]'";
		} else if ($calm > 1) {
			for ($i = 1; $i < $calm; $i++)
				$aux .= " OR CodUbic='$alm[$i]'";
			$cond = "and (CodUbic='$alm[0]' $aux)";
		}
		$sql = "SELECT CodItem AS coditem, Cantidad AS cantidad, esunid AS esunid FROM SAITEMFAC WHERE NumeroD IN (SELECT fa.NumeroR FROM SAFACT AS fa WHERE TipoFac='A' " . $cond . " AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN $fechai AND $fechaf
                        AND (NumeroR IS NULL OR NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN $fechai AND $fechaf GROUP BY x.NumeroR HAVING CAST(SUM(x.Monto) AS INT) < CAST(fa.Monto AS INT)))
                        AND NumeroD NOT IN (SELECT numeros FROM appfacturas_det)) AND tipofac = 'B'";

		//PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
	}
}
