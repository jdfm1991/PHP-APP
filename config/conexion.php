<?php
date_default_timezone_set('America/Caracas');
include_once "const.php";
include_once "email.php";
//if (is_array($_SESSION)) {
    include_once (PATH_HELPERS_PHP . "php/index.php");
    include_once (PATH_HELPERS_PHP . "sql/index.php");
    # servicios
    include_once (PATH_SERVICE_PHP . "index.php");
//}

class Conectar {
	protected $dbh;
	protected function conexion() {
		try {
			$conectar = $this->dbh = new PDO("sqlsrv:Server=localhost;Database=appwebaj","sa","merumbd4z");
			return $conectar;
		} catch (Exception $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	}

	protected function conexion2() {
		try {
//			$conectar = $this->dbh = new PDO("sqlsrv:Server=localhost;Database=aj","sa","merumbd4z");
			$conectar = $this->dbh = new PDO("sqlsrv:Server=192.168.7.31;Database=aj","sa","Confisur1");
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
		return URL_APP;
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