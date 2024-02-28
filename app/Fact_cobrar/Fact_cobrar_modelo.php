
<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Fact_cobrar extends Conectar
{
	public function get_facturasPorCobrar($edv)
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
		$sql = "SELECT (case when saacxc.tipocxc = 10 then 'FACT' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
                CONVERT( date , saacxc.fechae ) as FechaEmi, 
                (case when saacxc.tipocxc = 10 then (select CONVERT( VARCHAR ,fechad,103) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
                  appfacturas_det.numeros = saacxc.numerod) else 'N/A' end) as FechaDesp,
                  DATEDIFF(DD, saacxc.fechae, (case when saacxc.tipocxc = 10 then (select CONVERT( date ,GETDATE()) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
                  appfacturas_det.numeros = saacxc.numerod) else saacxc.fechae end))as DiasTrans,
                DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
                UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, (saacxc.saldo/nullif(SAFACT.Tasa, 0)) as SaldoPendolar,
                (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
                from saacxc inner join saclie on saacxc.codclie = saclie.codclie inner join SAFACT on SAFACT.NumeroD= SAACXC.NumeroD 
                where (DATEADD(dd, 0, DATEDIFF(dd, 0, SAACXC.FechaE)) between DATEADD(day, -7, CONVERT( date ,GETDATE())) and DATEADD(day, 0, CONVERT( date ,GETDATE()))) 
                and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') $vendedor

		UNION
  
    SELECT (case when saacxc.tipocxc = 10 then 'FACT' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
                CONVERT( date , saacxc.fechae ) as FechaEmi, 
                (case when saacxc.tipocxc = 10 then (select CONVERT( VARCHAR ,fechad,103) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
                  appfacturas_det.numeros = saacxc.numerod) else 'N/A' end) as FechaDesp,
                  DATEDIFF(DD, saacxc.fechae, (case when saacxc.tipocxc = 10 then (select CONVERT( date ,GETDATE()) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
                  appfacturas_det.numeros = saacxc.numerod) else saacxc.fechae end))as DiasTrans,
                DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
                UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, (saacxc.saldo/nullif(SAFACT.Tasa, 0)) as SaldoPendolar,
                (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
                from saacxc inner join saclie on saacxc.codclie = saclie.codclie inner join SAFACT on SAFACT.NumeroD= SAACXC.NumeroD 
                where (DATEADD(dd, 0, DATEDIFF(dd, 0, SAACXC.FechaE)) between DATEADD(day, -15, CONVERT( date ,GETDATE())) and DATEADD(day, -8, CONVERT( date ,GETDATE()))) 
                and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') $vendedor

		UNION
		  
     SELECT (case when saacxc.tipocxc = 10 then 'FACT' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
                CONVERT( date , saacxc.fechae ) as FechaEmi, 
                (case when saacxc.tipocxc = 10 then (select CONVERT( VARCHAR ,fechad,103) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
                  appfacturas_det.numeros = saacxc.numerod) else 'N/A' end) as FechaDesp,
                  DATEDIFF(DD, saacxc.fechae, (case when saacxc.tipocxc = 10 then (select CONVERT( date ,GETDATE()) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
                  appfacturas_det.numeros = saacxc.numerod) else saacxc.fechae end))as DiasTrans,
                DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
                UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, (saacxc.saldo/nullif(SAFACT.Tasa, 0)) as SaldoPendolar,
                (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
                from saacxc inner join saclie on saacxc.codclie = saclie.codclie inner join SAFACT on SAFACT.NumeroD= SAACXC.NumeroD 
                where (DATEADD(dd, 0, DATEDIFF(dd, 0, SAACXC.FechaE)) between DATEADD(day, -40, CONVERT( date ,GETDATE())) and DATEADD(day, -16, CONVERT( date ,GETDATE()))) and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') 
                $vendedor
		
		UNION		
				
	SELECT (case when saacxc.tipocxc = 10 then 'FACT' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
        CONVERT( date , saacxc.fechae ) as FechaEmi, 
        (case when saacxc.tipocxc = 10 then (select CONVERT( VARCHAR ,fechad,103) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
          appfacturas_det.numeros = saacxc.numerod) else 'N/A' end) as FechaDesp,
          DATEDIFF(DD, saacxc.fechae, (case when saacxc.tipocxc = 10 then (select CONVERT( date ,GETDATE()) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
          appfacturas_det.numeros = saacxc.numerod) else saacxc.fechae end))as DiasTrans,
        DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
         UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, (saacxc.saldo/nullif(SAFACT.Tasa, 0)) as SaldoPendolar,
         (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
         from saacxc inner join saclie on saacxc.codclie = saclie.codclie inner join SAFACT on SAFACT.NumeroD= SAACXC.NumeroD 
         where (SAACXC.FechaE < DATEADD(day, -40, CONVERT( date ,GETDATE()))) and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') 
         $vendedor
		 
		 UNION
		 
		 SELECT (case when saacxc.tipocxc = 10 then 'FACT' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
                CONVERT( date , saacxc.fechae ) as FechaEmi, 
                (case when saacxc.tipocxc = 10 then (select CONVERT( VARCHAR ,fechad,103) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
                  appfacturas_det.numeros = saacxc.numerod) else 'N/A' end) as FechaDesp,
                  DATEDIFF(DD, saacxc.fechae, (case when saacxc.tipocxc = 10 then (select CONVERT( date ,GETDATE()) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
                  appfacturas_det.numeros = saacxc.numerod) else saacxc.fechae end))as DiasTrans,
                DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
                UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend,  (saacxc.saldo/nullif(SAFACT.Tasa, 0)) as SaldoPendolar,
                (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
                from saacxc inner join saclie on saacxc.codclie = saclie.codclie inner join SAFACT on SAFACT.NumeroD= SAACXC.NumeroD 
                where saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') $vendedor order by ruta asc";

		//PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
        
		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
	}
}
