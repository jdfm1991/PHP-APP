<?php
date_default_timezone_set('America/Caracas');
include_once "const.php";
//if (!empty($_SESSION)) {
    include_once "../helpers/php/index.php";
    include_once "../helpers/sql/index.php";
//}
class Conectar {
	protected $dbh;
	protected function conexion(){
		try {
			$conectar = $this->dbh = new PDO("sqlsrv:Server=localhost;Database=appwebaj","sa","merumbd4z");
			return $conectar;
		} catch (Exception $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	}
	protected function conexion2(){
		try {
			$conectar = $this->dbh = new PDO("sqlsrv:Server=localhost;Database=aj","sa","merumbd4z");
			return $conectar;
		} catch (Exception $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	}
	public function set_names(){
		return $this->dbh->query("SET NAMES 'utf8'");
	}
	public function ruta(){
		return "http://localhost/appweb/";
	}
protected function limpiar_cadena($cadena){
$cadena=trim($cadena);
$cadena=stripslashes($cadena);
$cadena=str_ireplace("<script>","",$cadena);
$cadena=str_ireplace("</script>","",$cadena);
$cadena=str_ireplace("<script src","",$cadena);
$cadena=str_ireplace("<script type=","",$cadena);
$cadena=str_ireplace("SELECT * FROM","",$cadena);
$cadena=str_ireplace("DELETE FROM","",$cadena);
$cadena=str_ireplace("INSERT INTO","",$cadena);
$cadena=str_ireplace("--","",$cadena);
$cadena=str_ireplace("[","",$cadena);
$cadena=str_ireplace("]","",$cadena);
$cadena=str_ireplace("==","",$cadena);
return $cadena;
}
}