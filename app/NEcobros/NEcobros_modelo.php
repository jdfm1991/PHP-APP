
<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class NEcobros extends Conectar
{
	public function getNotasPorCobrar($edv)
	{
		//LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
		//CUANDO ES APPWEB ES CONEXION.
		$conectar = parent::conexion2();
		parent::set_names();

		//armamos una lista de los depositos, si no existe ninguno seleccionado no se considera para realizar la consulta
		$depo = "(" . substr($edv, 0, strlen($edv) - 1) . ")";


		if ($depo != "()" and $depo != "('TODOS')") {
			$vendedor = "AND SAACXC.CodVend IN " . $depo;
		} else {
			$vendedor = "";
		}

		//QUERY
		$sql = "SELECT (case when saacxc.tipocxc = 10 then 'NE' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
            CONVERT( date , saacxc.fechae ) as FechaEmi, 
            DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
             UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, 
             (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
             from CONFIMANIA_D.dbo.saacxc inner join saclie on saacxc.codclie = saclie.codclie 
             where (DATEADD(dd, 0, DATEDIFF(dd, 0, SAACXC.FechaE)) between DATEADD(day, -7, CONVERT( date ,GETDATE())) and GETDATE()) and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') $vendedor
    UNION

	SELECT (case when saacxc.tipocxc = 10 then 'NE' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
          CONVERT( date , saacxc.fechae ) as FechaEmi, 
          DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
           UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, 
           (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
           from CONFIMANIA_D.dbo.saacxc inner join saclie on saacxc.codclie = saclie.codclie 
           where (DATEADD(dd, 0, DATEDIFF(dd, 0, SAACXC.FechaE)) between DATEADD(day, -15, CONVERT( date ,GETDATE())) and DATEADD(day, -8, CONVERT( date ,GETDATE()))) 
           and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') $vendedor 
   UNION
           
SELECT (case when saacxc.tipocxc = 10 then 'NE' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
          CONVERT( date , saacxc.fechae ) as FechaEmi, 
          DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
           UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, 
           (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
           from CONFIMANIA_D.dbo.saacxc inner join saclie on saacxc.codclie = saclie.codclie 
           where (DATEADD(dd, 0, DATEDIFF(dd, 0, SAACXC.FechaE)) between DATEADD(day, -40, CONVERT( date ,GETDATE())) and DATEADD(day, -16, CONVERT( date ,GETDATE()))) and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') $vendedor
      
	  UNION
	      
SELECT (case when saacxc.tipocxc = 10 then 'NE' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
            CONVERT( date , saacxc.fechae ) as FechaEmi, 
            DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
            UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, 
            (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
            from CONFIMANIA_D.dbo.saacxc inner join saclie on saacxc.codclie = saclie.codclie 
            where (SAACXC.FechaE < DATEADD(day, -40, CONVERT( date ,GETDATE()))) and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') $vendedor
			order by ruta asc";

		//PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
        
		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
	}
}
