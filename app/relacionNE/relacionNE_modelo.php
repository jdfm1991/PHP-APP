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
              $sql = "SELECT a.numerod, a.tipofac, a.codclie, a.rif, a.rsocial, a.direccion, b.direc2 as direccion2, a.codvend, a.total, a.fechae, a.subtotal FROM sanota as a inner join saclie as b on a.codclie=b.codclie  WHERE a.TipoFac='C' AND a.fechae between '$fechai' and '$fechaf' and a.codvend='$ruta' ";
            }else{
              $sql = "SELECT a.numerod, a.tipofac, a.codclie, a.rif, a.rsocial, a.direccion, b.direc2 as direccion2, a.codvend, a.total, a.fechae, a.subtotal FROM sanota as a inner join saclie as b on a.codclie=b.codclie  WHERE a.TipoFac='C' AND a.fechae between '$fechai' and '$fechaf'";
            }

         /*   $sql = "SELECT * from [AJ].[dbo].[SAACXP] inner join [AJ].[dbo].[SAPROV] on [SAACXP].codprov
           = [SAPROV].codprov where  [SAACXP].fechae between '$fechai' and '$fechaf' and [SAACXP].tipocxp='10' and [SAACXP].saldo>0 and [SAACXP].codprov = '$codprov' order by [SAACXP].fechae desc";
          */

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



}