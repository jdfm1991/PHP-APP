<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("listadeprecio_modelo.php");


$depos = $_POST['depo'];
$marcas = $_POST['marca'];
$orden = $_POST['orden'];
$exis = $_POST['exis'];
$iva = $_POST['iva'];
$cubi = $_POST['cubi'];
$p1 = str_replace("1","1",$_POST['p1']);
$p2 = str_replace("1","2",$_POST['p2']);
$p3 = str_replace("1","3",$_POST['p3']);
$sumap = $_POST['p1'] + $_POST['p2'] + $_POST['p3'];
$sumap2 = $p1 + $p2 + $p3;

//INSTANCIAMOS EL MODELO
$listadeprecio  = new Listadeprecio();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_listadeprecio":

    $datos = $listadeprecio ->getListaDePrecios($_POST['marca'],$_POST['depo'],$_POST['exis'],$_POST['orden']);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
    $data = Array();


    foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();
        /*$sub_array[] = date("d-m-Y",strtotime($row["fechauv"]));*/


        $sub_array[] = $row["codvend"];
        $sub_array[] = $row["codclie"];
        $sub_array[] = $row["descrip"];
        $sub_array[] = date("d-m-Y",strtotime($row["fecha"]));
        $sub_array[] = $row["rif"];
        $sub_array[] = $row["dvisita"];
        $sub_array[] = $row["codnestle"];

        $data[] = $sub_array;

    }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
    $results = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);
    echo json_encode($results);

    break;


}
