<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class estadocuenta extends Conectar{


	public function getestadocuenta($fechai, $fechaf , $cliente, $tipo){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION. tipocxc, fechae, tipop, fechap,tipofac,estado,mtotalbs,tasa,mtotald,mabonado,moneda,nc,saldoact,numerod,referencia,detalle
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY
        $actual =date('Y-m-d');

        if($tipo == 'H'){

           $sql = "SELECT  [TipoCxP]
            ,[SAACXP].[CodProv]
            ,[SAACXP].Descrip
            ,[SAACXP].[FechaE]
            ,[FechaV]
            ,[NumeroD]
            ,[NumeroN]
            ,[Document]
            ,[Monto]
            ,[SaldoAct]
            ,DATEDIFF(day, [SAACXP].FechaV, '$actual') DiasTrans
            FROM [SAACXP] inner join SAPROV on SAPROV.CodProv=[SAACXP].CodProv  where [SAACXP].CodProv = '$cliente' AND DATEADD(dd, 0, DATEDIFF(dd, 0, [SAACXP].fechae)) between '$fechai' and '$fechaf'";


        }else{


           $sql = "SELECT  [TipoCxP]
           ,[SAACXP].[CodProv]
           ,[SAACXP].Descrip
           ,[SAACXP].[FechaE]
           ,[FechaV]
           ,[NumeroD]
           ,[NumeroN]
           ,[Document]
           ,[Monto]
           ,[SaldoAct]
           ,DATEDIFF(day, [SAACXP].FechaV, '$actual') DiasTrans
           FROM [CONFIMANIA_D].[dbo].[SAACXP] inner join SAPROV on SAPROV.CodProv=[SAACXP].CodProv  where [SAACXP].CodProv = '$cliente' AND DATEADD(dd, 0, DATEDIFF(dd, 0, [SAACXP].fechae)) between '$fechai' and '$fechaf'";




        }

           
        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
       $sql->execute();
       $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
   }

   public function get_documento_pago($fechai, $fechaf , $cliente, $tipo, $documento){

      //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
      //CUANDO ES APPWEB ES CONEXION. tipocxc, fechae, tipop, fechap,tipofac,estado,mtotalbs,tasa,mtotald,mabonado,moneda,nc,saldoact,numerod,referencia,detalle
     $conectar= parent::conexion2();
     parent::set_names();

      //QUERY
      $actual =date('Y-m-d');

      if($tipo == 'H'){

         $sql = "SELECT  NumeroN,NumeroD
          FROM [SAACXP] inner join SAPROV on SAPROV.CodProv=[SAACXP].CodProv  where [SAACXP].CodProv = '$cliente' AND DATEADD(dd, 0, DATEDIFF(dd, 0, [SAACXP].fechae)) between '$fechai' and '$fechaf' and TipoCxP='41'";


      }else{


         $sql = "SELECT 
         NumeroN,NumeroD
         FROM [CONFIMANIA_D].[dbo].[SAACXP] inner join SAPROV on SAPROV.CodProv=[SAACXP].CodProv  where [SAACXP].CodProv = '$cliente' AND DATEADD(dd, 0, DATEDIFF(dd, 0, [SAACXP].fechae)) between '$fechai' and '$fechaf' and TipoCxP='41'";




      }

         
      //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
     $sql = $conectar->prepare($sql);
     $sql->execute();
     $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    
     return $result ;
 }


}