
<?php
 //LLAMAMOS A LA CONEXION.
require_once("../acceso/conexion.php");

class CostodeInventario extends Conectar{

	public function get_marcas(){

		$conectar=parent::conexion2();
		parent::set_names();

		$sql="SELECT DISTINCT(marca) FROM saprod WHERE activo = '1'  order by marca asc";

		$sql=$conectar->prepare($sql);
		$sql->execute();

		return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
	}

	public function get_Almacenes(){

		$conectar=parent::conexion2();
		parent::set_names();

		$sql="SELECT CodUbic AS codubi, Descrip AS descrip FROM sadepo ORDER BY codubic";
		$sql=$conectar->prepare($sql);
		$sql->execute();

		return $resultado=$sql->fetchAll();
	}

	public function getCostosdEinventario($edv, $marca){

		 //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
		 //CUANDO ES APPWEB ES CONEXION.
		$conectar= parent::conexion2();
		parent::set_names();

 		//QUERY
		$depo = "(" . substr($edv, 0, strlen($edv) - 1) . ")";
		if ($depo != "()") { $codubic = "AND saexis.codubic IN " . $depo; } else { $codubic = ""; }
		if ($marca != "-") { $q_marca = "AND marca LIKE '$marca'"; } else { $q_marca = ""; }

		$sql = "SELECT saexis.codprod AS codprod, CantEmpaq AS display, Descrip AS descrip, Tara AS tara, Marca AS marca, CostAct AS costo, precio1 AS precio, SUM(saexis.existen) AS bultos,SUM(saexis.exunidad) AS paquetes
		FROM saprod INNER JOIN saexis ON saprod.codprod = saexis.codprod
		WHERE (saexis.existen > 0 OR saexis.exunidad > 0) AND len(marca) > 0 $codubic $q_marca
		GROUP BY saexis.codprod, CantEmpaq, descrip, CostAct, precio1, Marca, tara";

		 //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
	}

}

