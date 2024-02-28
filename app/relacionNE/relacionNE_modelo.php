<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class relacionNE extends Conectar{


	public function getdevolucionnotaentrega($fechai, $fechaf, $ruta){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY
            if($ruta != 'Todos'){
              $sql = "SELECT numerod, codclie as rif, codvend, rsocial, fechae, total, estatus, numerof, numerodv, descuento, subtotal, abono, tipofac FROM sanota where tipofac in ('C') AND fechae between '$fechai' and '$fechaf' and codvend='$ruta' ";
            }else{
              $sql = "SELECT numerod, codclie as rif, codvend, rsocial, fechae, total, estatus, numerof, numerodv, descuento, subtotal, abono, tipofac FROM sanota where tipofac in ('C') AND fechae between '$fechai' and '$fechaf'";
            }

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
       $sql->execute();
       $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
   }

   function dias_transcurridos($fecha_i,$fecha_f)
{
	$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
	$dias 	= abs($dias); $dias = floor($dias);		
	return $dias;
}


public function getmontoDEV($numerodv){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY
           
              $sql = "SELECT total as total from SANOTA where numerod = '$numerodv' and tipofac = 'D'";
            

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
       $sql->execute();
       $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
   }


   public function get_descuentosanota($numerod){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY
           
              $sql = "SELECT descuento as descuento from SANOTA where numerod = '$numerod' and tipofac = 'C'";
            

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
       $sql->execute();
       $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
   }


   public function get_descuentosaitemnota($numerod){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY
           
              $sql = "SELECT descuento as descuento from SAITEMNOTA where numerod = '$numerod' and tipofac = 'C'";
            

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
       $sql->execute();
       $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
   }



}