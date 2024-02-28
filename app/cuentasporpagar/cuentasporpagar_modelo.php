<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class NEporcobrar extends Conectar{


	public function getNEporcobrar($fechai, $fechaf){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY

          $sql = "SELECT 
          (select Descrip from CONFIMANIA_D.dbo.SACONF) as Empresa,
          SUM(case when (DATEDIFF(DD, SAACXP.FechaE, GETDATE())>=0 and DATEDIFF(DD, SAACXP.FechaE, GETDATE())<=7) then SAACXP.Saldo else 0 end) as Total_0_a_7_Dias,
          SUM(case when (DATEDIFF(DD, SAACXP.FechaE, GETDATE())>=8 and DATEDIFF(DD, SAACXP.FechaE, GETDATE())<=15) then SAACXP.Saldo else 0 end) as Total_8_a_15_Dias,
          SUM(case when (DATEDIFF(DD, SAACXP.FechaE, GETDATE())>=16 and DATEDIFF(DD, SAACXP.FechaE, GETDATE())<=40) then SAACXP.Saldo else 0 end) as Total_16_a_40_Dias,
          SUM(case when (DATEDIFF(DD, SAACXP.FechaE, GETDATE())>40) then SAACXP.Saldo else 0 end) as Total_Mayor_a_40_Dias,
          SUM(SAACXP.Saldo) as SubTotal
           from CONFIMANIA_D.dbo.SAACXP inner join CONFIMANIA_D.dbo.SAPROV on SAACXP.CodProv = SAPROV.CodProv 
           where SAACXP.saldo>0 AND (SAACXP.TipoCxP='10' OR SAACXP.TipoCxP='20')" ;

        


        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
       $sql->execute();
       $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
   }


   public function getdetallesNEporcobrar($fechai, $fechaf, $data){

      //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
      //CUANDO ES APPWEB ES CONEXION.
     $conectar= parent::conexion2();
     parent::set_names();
  
      //QUERY
  
      switch ($data) {
  
        case 1:
  
          $sql = "SELECT (case when SAACXP.tipocxp = 10 then 'NE' else 'N/D' end) as TipoOpe, SAACXP.numerod as NroDoc ,SAPROV.Telef as telefono, SAPROV.CodProv as CodClie, SAPROV.Descrip as Cliente, 
          CONVERT( date , SAACXP.fechae ) as FechaEmi, DATEDIFF(DD, SAACXP.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy, SAACXP.saldo as SaldoPend
           from CONFIMANIA_D.dbo.SAACXP inner join SAPROV on SAACXP.CodProv = SAPROV.CodProv 
           where (DATEADD(dd, 0, DATEDIFF(dd, 0, SAACXP.FechaE)) between DATEADD(day, -7, CONVERT( date ,GETDATE())) and GETDATE()) and SAACXP.saldo>0 AND (SAACXP.TipoCxP='10' OR SAACXP.TipoCxP='20') 
           order by SAACXP.FechaE asc";
    
        
        break;
    
    
        case 2:
  
          $sql = "SELECT (case when SAACXP.TipoCxP = 10 then 'NE' else 'N/D' end) as TipoOpe, SAACXP.numerod as NroDoc ,SAPROV.Telef as telefono, SAPROV.CodProv as CodClie, SAPROV.Descrip as Cliente, 
          CONVERT( date , SAACXP.fechae ) as FechaEmi, 
          DATEDIFF(DD, SAACXP.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,SAACXP.saldo as SaldoPend
           from CONFIMANIA_D.dbo.SAACXP inner join SAPROV on SAACXP.CodProv = SAPROV.CodProv 
           where (DATEADD(dd, 0, DATEDIFF(dd, 0, SAACXP.FechaE)) between DATEADD(day, -15, CONVERT( date ,GETDATE())) and DATEADD(day, -8, CONVERT( date ,GETDATE()))) 
           and SAACXP.saldo>0 AND (SAACXP.TipoCxP='10' OR SAACXP.TipoCxP='20') 
           order by SAACXP.FechaE asc";
            
        break;
        
        
        case 3:
  
          $sql = "SELECT (case when SAACXP.TipoCxP = 10 then 'NE' else 'N/D' end) as TipoOpe, SAACXP.numerod as NroDoc ,SAPROV.Telef as telefono, SAPROV.CodProv as CodClie, SAPROV.Descrip as Cliente, 
          CONVERT( date , SAACXP.fechae ) as FechaEmi, 
          DATEDIFF(DD, SAACXP.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy, SAACXP.saldo as SaldoPend
           from CONFIMANIA_D.dbo.SAACXP inner join SAPROV on SAACXP.CodProv = SAPROV.CodProv 
           where (DATEADD(dd, 0, DATEDIFF(dd, 0, SAACXP.FechaE)) between DATEADD(day, -40, CONVERT( date ,GETDATE())) and DATEADD(day, -16, CONVERT( date ,GETDATE()))) and SAACXP.saldo>0 AND (SAACXP.TipoCxP='10' OR SAACXP.TipoCxP='20') 
           order by SAACXP.FechaE asc";
            
        break;
          
          
        case 4:
  
          $sql = "SELECT (case when SAACXP.TipoCxP = 10 then 'NE' else 'N/D' end) as TipoOpe, SAACXP.numerod as NroDoc ,SAPROV.Telef as telefono, SAPROV.CodProv as CodClie, SAPROV.Descrip as Cliente, 
          CONVERT( date , SAACXP.fechae ) as FechaEmi, 
          DATEDIFF(DD, SAACXP.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy, SAACXP.saldo as SaldoPend
           from CONFIMANIA_D.dbo.SAACXP inner join SAPROV on SAACXP.CodProv = SAPROV.CodProv 
           where (SAACXP.FechaE < DATEADD(day, -40, CONVERT( date ,GETDATE()))) and SAACXP.saldo>0 AND (SAACXP.TipoCxP='10' OR SAACXP.TipoCxP='20') 
           order by SAACXP.FechaE asc";
    
            
        break;
           
           
        case 5:
  
          $sql = "SELECT (case when SAACXP.TipoCxP = 10 then 'NE' else 'N/D' end) as TipoOpe, SAACXP.numerod as NroDoc ,SAPROV.Telef as telefono, SAPROV.CodProv as CodClie, SAPROV.Descrip as Cliente, 
          CONVERT( date , SAACXP.fechae ) as FechaEmi, 
          DATEDIFF(DD, SAACXP.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy, SAACXP.saldo as SaldoPend 
           from CONFIMANIA_D.dbo.SAACXP inner join SAPROV on SAACXP.CodProv = SAPROV.CodProv 
           where SAACXP.saldo>0 AND (SAACXP.TipoCxP='10' OR SAACXP.TipoCxP='20') 
           order by SAACXP.FechaE asc";
    
            
        break;
    }
  
      //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
     $sql = $conectar->prepare($sql);
     $sql->execute();
     $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    
     return $result ;
  }


}