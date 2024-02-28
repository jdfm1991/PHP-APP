<?php
set_time_limit(0);
//LLAMAMOS A LA conexion2.
require_once("../../config/conexion.php");

class DataVendedores extends Conectar
{

     
    public function InsertSQL($tabla, $campos, $valores) {

        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY

        $sql=("INSERT into $tabla ($campos) VALUES($valores)");

		$sql = $conectar->prepare($sql);
         $result =$sql->execute();

         if (!$result) {
                echo "\nPDO::errorInfo():\n";
                print_r($sql->errorInfo());
        }

        return $result;
    }
    

    public function DeleteSQL($tabla, $condicion) {
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY

        $sql=("DELETE from $tabla where $condicion");

         //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		 $sql = $conectar->prepare($sql);
         return $sql->execute();

    }


    public function UpdateSQL($tabla, $campos, $condicion) {
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY

        $sql=("UPDATE $tabla set $campos where $condicion");
		$sql = $conectar->prepare($sql);
		return $sql->execute();
    
    }


    public function consultaSQL($consulta){
        
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
            $sql="$consulta";

        
        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);

       /* if (!$result) {
                echo "\nPDO::errorInfo():\n";
                print_r($sql->errorInfo());
        }*/

         return $result;

    }



}
?>
