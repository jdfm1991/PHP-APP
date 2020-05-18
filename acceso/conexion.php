<?php

date_default_timezone_set('America/Caracas');
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();

class Conectar {

	protected $dbh;

	protected function conexion(){


		try {

			$conectar = $this->dbh = new PDO("sqlsrv:Server=192.168.0.10;Database=appwebaj","sa","merumbd4z");

			return $conectar;

		} catch (Exception $e) {

			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	}

	protected function conexion2(){

		try {

			$conectar = $this->dbh = new PDO("sqlsrv:Server=192.168.0.10;Database=aj","sa","merumbd4z");

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

	public static function convertir($string){

		$string = str_replace(
			array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'),
			array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', ' DICIEMBRE'),
			$string
		);
		return $string;
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
