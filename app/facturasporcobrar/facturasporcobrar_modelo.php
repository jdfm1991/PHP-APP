<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class facturasporcobrar extends Conectar{


	public function getfacturasporcobrar($fechai, $fechaf){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY

        if ($fechai == "" and $fechaf == ""){

          $sql = "SELECT (case when saacxc.tipocxc = 10 then 'FACT' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
          CONVERT( date , saacxc.fechae ) as FechaEmi, 
          (case when saacxc.tipocxc = 10 then (select CONVERT( VARCHAR ,fechad,103) from [AJ].[dbo].appfacturas inner join [AJ].[dbo].appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
              appfacturas_det.numeros = saacxc.numerod) else 'N/A' end) as FechaDesp,
              DATEDIFF(DD, saacxc.fechae, (case when saacxc.tipocxc = 10 then (select CONVERT( date ,GETDATE()) from [AJ].[dbo].appfacturas inner join [AJ].[dbo].appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
              appfacturas_det.numeros = saacxc.numerod) else saacxc.fechae end))as DiasTrans,
          DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,(case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=0 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=7) then SAACXC.Saldo else 0 end) as De_0_a_7_Dias,
        (case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=8 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=14) then SAACXC.Saldo else 0 end) as De_8_a_14_Dias ,
        (case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=15 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=21) then SAACXC.Saldo else 0 end) as De_15_a_21_Dias ,
        (case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=22 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=31) then SAACXC.Saldo else 0 end) as De_22_a_31_Dias ,
        (case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=32 ) then SAACXC.Saldo else 0 end) as Mas_31_Dias ,
        saacxc.saldo as SaldoPend, UPPER(saacxc.codvend) as Ruta,
           (select Coordinador from [AJ].[dbo].SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
           from [AJ].[dbo].saacxc inner join [AJ].[dbo].saclie on saacxc.codclie = saclie.codclie 
           where saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') 
           order by saacxc.FechaE asc" ;

          }else{

            $sql = "SELECT (case when saacxc.tipocxc = 10 then 'FACT' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
            CONVERT( date , saacxc.fechae ) as FechaEmi, 
            (case when saacxc.tipocxc = 10 then (select CONVERT( VARCHAR ,fechad,103) from [AJ].[dbo].appfacturas inner join [AJ].[dbo].appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
                appfacturas_det.numeros = saacxc.numerod) else 'N/A' end) as FechaDesp,
                DATEDIFF(DD, saacxc.fechae, (case when saacxc.tipocxc = 10 then (select CONVERT( date ,GETDATE()) from [AJ].[dbo].appfacturas inner join [AJ].[dbo].appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
                appfacturas_det.numeros = saacxc.numerod) else saacxc.fechae end))as DiasTrans,
            DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,(case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=0 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=7) then SAACXC.Saldo else 0 end) as De_0_a_7_Dias,
             (case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=8 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=14) then SAACXC.Saldo else 0 end) as De_8_a_14_Dias ,
             (case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=15 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=21) then SAACXC.Saldo else 0 end) as De_15_a_21_Dias ,
             (case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=22 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=31) then SAACXC.Saldo else 0 end) as De_22_a_31_Dias ,
             (case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=32 ) then SAACXC.Saldo else 0 end) as Mas_31_Dias ,
             saacxc.saldo as SaldoPend, UPPER(saacxc.codvend) as Ruta,
             (select Coordinador from [AJ].[dbo].SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
             from [AJ].[dbo].saacxc inner join [AJ].[dbo].saclie on saacxc.codclie = saclie.codclie 
             where saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') and saacxc.FechaE between '$fechai' and '$fechaf' 
             order by saacxc.FechaE asc";
          
          } 


        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
       $sql->execute();
       $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
   }


}