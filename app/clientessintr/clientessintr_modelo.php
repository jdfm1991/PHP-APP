
<?php
//LLAMAMOS A LA CONEXION.
require_once("../acceso/conexion.php");

class ClientesSintr extends Conectar
{

	public function getclientessintr($fechai, $fechaf, $vendedor)
	{

		//LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
		//CUANDO ES APPWEB ES CONEXION.
		$conectar = parent::conexion2();
		parent::set_names();

		//QUERY
		$sql =   "SELECT codclie, descrip, codvend, (SELECT COALESCE(SUM(saldo), 0) FROM saacxc WHERE codclie= saclie.CodClie AND tipocxc='10' AND saldo>0) AS debe
		FROM saclie
		WHERE activo = 1
		AND SACLIE.CodClie NOT IN (";
		if ($vendedor != "-") {
			$sql .= "SELECT CodClie FROM safact WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, safact.FechaE)) BETWEEN ? AND ? AND safact.codvend = ? AND codclie = SACLIE.CodClie AND tipofac = 'A'";
		} else {
			$sql .= "SELECT CodClie FROM safact WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, safact.FechaE)) BETWEEN ? AND ?  AND codclie = SACLIE.CodClie AND tipofac = 'A'";
		}
		$sql .= ") ORDER BY codvend";

		//PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
		$sql->bindValue(1, $fechai);
		$sql->bindValue(2, $fechaf);
		$sql->bindValue(3, $vendedor);

		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
	}
}
