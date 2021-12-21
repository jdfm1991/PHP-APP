
<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Listadepreciodivisas extends Conectar{

	public function getListadeprecios($marca, $depos, $exis, $orden){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
		$conectar= parent::conexion2();
		parent::set_names();

 		//QUERY
        $sql = "";
		 if(hash_equals("-", $marca)) { //todas las marcas
		 	if(hash_equals("-", $depos)) {
		 		if(hash_equals("0", $exis)) {
		 			$sql = "SELECT saprod.CodProd, saprod.Descrip,  saprod.marca, saexis.Existen AS existen, saprod_02.Precio1_B AS precio1,
                                   saprod_02.Precio2_B AS precio2, saprod_02.Precio3_B AS precio3, saexis.ExUnidad AS exunidad,
                                   saprod_02.Precio1_P AS preciou1, saprod_02.Precio2_P AS preciou2 ,  saprod_02.Precio3_P AS PrecioU3, saprod.esexento
                            FROM saexis
                                INNER JOIN saprod ON saexis.codprod = saprod.codprod
                                LEFT JOIN saprod_02 ON saprod.codprod = saprod_02.codprod
                            WHERE (saexis.codubic = '01' OR saexis.codubic = '20' OR saexis.codubic = '30')
                            GROUP BY SAPROD.CodProd, saprod_02.Precio1_B, saprod_02.Precio2_B, saprod_02.Precio3_B, saprod_02.Precio1_P, saprod_02.Precio2_P, saprod_02.Precio3_P, saprod.Descrip, saprod.marca, saexis.Existen, saexis.ExUnidad, saprod.esexento
                            ORDER BY saprod.$orden";
		 		} else {
		 			$sql = "SELECT saprod.CodProd, saprod.Descrip,  saprod.marca, saexis.Existen AS existen, saprod_02.Precio1_B AS precio1,
                                   saprod_02.Precio2_B AS precio2, saprod_02.Precio3_B AS precio3, saexis.ExUnidad AS exunidad,
                                   saprod_02.Precio1_P AS preciou1, saprod_02.Precio2_P AS preciou2 ,  saprod_02.Precio3_P AS PrecioU3, saprod.esexento
                            FROM saexis 
                                INNER JOIN saprod ON saexis.codprod = saprod.codprod
                                LEFT JOIN saprod_02 ON saprod.codprod = saprod_02.codprod
                            WHERE (saexis.codubic = '01' OR saexis.codubic = '20' OR saexis.codubic = '30') AND (saexis.existen > 0 OR saexis.exunidad > 0)
                            GROUP BY SAPROD.CodProd, saprod_02.Precio1_B, saprod_02.Precio2_B, saprod_02.Precio3_B, saprod_02.Precio1_P, saprod_02.Precio2_P, saprod_02.Precio3_P, saprod.Descrip, saprod.marca, saexis.Existen, saexis.ExUnidad, saprod.esexento 
                            ORDER BY saprod.$orden";
		 		}
		 	} else {
		 		if(hash_equals("0", $exis)) {
		 			$sql = "SELECT saprod.CodProd, saprod.Descrip,  saprod.marca, saexis.Existen AS existen, saprod_02.Precio1_B AS precio1, 
                                   saprod_02.Precio2_B AS precio2, saprod_02.Precio3_B AS precio3, saexis.ExUnidad AS exunidad, 
                                   saprod_02.Precio1_P AS preciou1, saprod_02.Precio2_P AS preciou2 ,  saprod_02.Precio3_P AS PrecioU3, saprod.esexento
                            FROM saexis 
                                INNER JOIN saprod ON saexis.codprod = saprod.codprod
                                LEFT JOIN saprod_02 ON saprod.codprod = SAPROD_02.codprod
                            WHERE (saexis.codubic = '$depos') 
                            ORDER BY saprod.$orden";
		 		} else {
		 			$sql = "SELECT saprod.CodProd, saprod.Descrip,  saprod.marca, saexis.Existen AS existen, saprod_02.Precio1_B AS precio1, 
                                   saprod_02.Precio2_B AS precio2, saprod_02.Precio3_B AS precio3, saexis.ExUnidad AS exunidad,
                                   saprod_02.Precio1_P AS preciou1, saprod_02.Precio2_P AS preciou2 ,  saprod_02.Precio3_P AS PrecioU3, saprod.esexento
                            FROM saexis 
                                INNER JOIN saprod ON saexis.codprod = saprod.codprod
                                LEFT JOIN saprod_02 ON saprod.codprod = SAPROD_02.codprod
                            WHERE (saexis.codubic = '$depos') AND (saexis.existen > 0 OR saexis.exunidad > 0) 
                            ORDER BY saprod.$orden";
		 		}
		 	}
        } else { //sino, por marca
        	if(hash_equals("-", $depos)) {
        		if(hash_equals("0", $exis)) {
        			$sql = " AND marca = '$marca' ";
        		} else {
        			$sql = " AND (saexis.existen > 0 OR saexis.exunidad > 0) AND marca = '$marca' ";
        		}
        	} else {
        		if(hash_equals("0", $exis)) {
        			$sql = "SELECT saprod.CodProd, saprod.Descrip,  saprod.marca, saexis.Existen AS existen, saprod_02.Precio1_B AS precio1,
                                   saprod_02.Precio2_B AS precio2, saprod_02.Precio3_B AS precio3, saexis.ExUnidad AS exunidad,
                                   saprod_02.Precio1_P AS preciou1, saprod_02.Precio2_P AS preciou2 ,  saprod_02.Precio3_P AS PrecioU3, saprod.esexento
                            FROM saexis 
                                INNER JOIN saprod ON saexis.codprod = saprod.codprod
                                LEFT JOIN saprod_02 ON saprod.codprod = saprod_02.codprod 
                            WHERE (saexis.codubic = '$depos') AND saprod.marca = '$marca' 
                            ORDER BY saprod.$orden";
        		} else {
        			$sql = "SELECT saprod.CodProd, saprod.Descrip,  saprod.marca, saexis.Existen AS existen, saprod_02.Precio1_B AS precio1, 
       saprod_02.Precio2_B AS precio2, saprod_02.Precio3_B AS precio3, saexis.ExUnidad AS exunidad,
       saprod_02.Precio1_P AS preciou1, saprod_02.Precio2_P AS preciou2 ,  saprod_02.Precio3_P AS PrecioU3, saprod.esexento
FROM saexis 
    inner join saprod ON saexis.codprod = saprod.codprod
    left join saprod_02 ON saprod.codprod = saprod_02.codprod
WHERE (saexis.codubic = '$depo') AND (saexis.existen > 0 OR saexis.exunidad > 0) AND saprod.marca = '$marca' 
GROUP BY SAPROD.CodProd, saprod_02.Precio1_B, saprod_02.Precio2_B, saprod_02.Precio3_B, saprod_02.Precio1_P, saprod_02.Precio2_P, saprod_02.Precio3_P, saprod.Descrip, saprod.marca, saprod.esexento, saexis.Existen, saexis.ExUnidad 
ORDER BY saprod.$orden";
        		}
        	}
        }

        switch ($opc) {
        	case 1:
        	$sql = "SELECT saprod.CodProd AS codprod, marca, Descrip AS descrip, Cubicaje AS cubicaje, SUM(saexis.Existen) AS existen, Precio1 AS precio1, Precio2 AS precio2, Precio3 AS precio3, SUM(saexis.ExUnidad) AS exunidad, PrecioU AS preciou1, PrecioU2 AS preciou2, PrecioU3 AS preciou3 
                        FROM saexis 
                            INNER JOIN saprod ON saexis.codprod = saprod.codprod 
                            LEFT JOIN saprod_01 ON saprod.codprod = saprod_01.codprod 
                    WHERE (saexis.codubic = '01' OR saexis.codubic = '20' OR saexis.codubic = '30') $condicion 
                    GROUP BY SAPROD.CodProd, Precio1, Precio2, Precio3, PrecioU, PrecioU2, PrecioU3, marca, Descrip 
                    ORDER BY saprod.$orden";
        	break;
        	case 2:
        	$sql = "SELECT Saexis.CodProd AS codprod, Saexis.Existen AS existen, EsExento AS esexento, Descrip AS descrip, Marca AS marca, Saexis.ExUnidad AS exunidad, Precio1 AS precio1, Precio2 AS precio2, Precio3 AS precio3, PrecioU2 AS preciou2, PrecioU3 AS preciou3, preciou AS preciou1, Cubicaje AS cubicaje 
                        FROM saexis 
                            INNER JOIN saprod ON saexis.codprod = saprod.codprod 
                            LEFT JOIN saprod_01 ON saprod.codprod = saprod_01.codprod 
                    WHERE (saexis.codubic = '$depos') $condicion 
                    ORDER BY saprod.$orden";
        	break;
        }

		 //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

}

