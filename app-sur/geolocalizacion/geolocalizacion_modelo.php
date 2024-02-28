<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Geolocalizacion extends Conectar
{
    public function getdata_cliente($opc, $vendedor ,$cond1 ,$cond2)
    {
        
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

    if ($opc == '0' and $vendedor == 'Todos') {

		$sql = ("SELECT s.codclie, latitud, longitud, DiasVisita, s.descrip, codvend from saclie_01 u inner join saclie s on s.codclie = u.codclie where (latitud is not null) and (longitud is not null) and latitud!='' and longitud!=''  and activo = 1 and codvend != '00'");
	}else{

		$sql = ("SELECT s.codclie, latitud, longitud, DiasVisita, s.descrip, codvend from saclie_01 u inner join saclie s on s.codclie = u.codclie where (latitud is not null) and (longitud is not null) and latitud!='' and longitud!=''  and activo = 1 and codvend != '00'".$cond1.$cond2);
	}

        $sql = $conectar->prepare($sql);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
        
    }
}
