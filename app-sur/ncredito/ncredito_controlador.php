<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("ncredito_modelo.php");

//INSTANCIAMOS EL MODELO
$credito = new Ncredito();
//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_ncredito":


        //echo "<script>console.log('Console:  PRUEBA' );</script>";

    $datos = $credito->get_ncredito( $_POST["fechai"], $_POST["fechaf"],$_POST["tipo"]);

    //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
    $data = Array();
    $fechai=$_POST["fechai"];
    $fechaf=$_POST["fechaf"];
    $tipodoc = $_POST["tipo"];
    $tipo='';
    $estado='';
     $saldoact = 0;
    foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

			$fechaE = date('d/m/Y', strtotime($row["FechaE"]));
			

        $mtotald = number_format($row["Monto"], 2, ',', '.');
        


        $sub_array[] = $row["CodClie"];
        $sub_array[] = $row["Descrip"];
        $sub_array[] = $fechaE;
        $sub_array[] = $row["NumeroD"];
        $sub_array[] = $row["NumeroN"];
        $sub_array[] = $row["Document"];
        $sub_array[] =  $mtotald;
           
         $saldoact+= $row["Monto"];
        
        $data[] = $sub_array;

    }

  

    //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
    $results = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data),
            'saldo' => number_format($saldoact, 2, ',', '.'), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);

    echo json_encode($results);
    break;

}