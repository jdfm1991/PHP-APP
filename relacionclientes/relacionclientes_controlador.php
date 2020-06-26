<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO
require_once("relacionclientes_modelo.php");

//INSTANCIAMOS EL MODELO
$relacion = new RelacionClientes();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "guardaryeditar":

        $id_usuario = isset($_POST["id_usuario"]);
        $cedula = isset($_POST["cedula"]);
        $login = isset($_POST["login"]);
        $nomper = isset($_POST["nomper"]);
        $email = isset($_POST["email"]);
        $clave = isset($_POST["clave"]);
        $rol = isset($_POST["rol"]);
        $estado = isset($_POST["estado"]);


        /*DATOS CLIENTE 1*/
        /*INSERT EN SACLIE SAINT*/
        $codclie = $_POST["codclie"];
        $descrip = $_POST["descrip"];
        $id3 = $_POST["id3"];
        $tipoid3 = $_POST["tipoid3"];
        $activo = $_POST["activo"];
        $clase = $_POST["clase"];
        $represent = $_POST["represent"];
        $direc1 = $_POST["direc1"];
        $direc2 = $_POST["direc2"];
        $telef = $_POST["telef"];
        $movil = $_POST["movil"];
        $email = $_POST["email"];
        $codzona = $_POST["codzona"];
        $codvend = $_POST["codvend"];
        $tipocli = $_POST["tipocli"];
        $observa = $_POST["observa"];
        $tipopvp = $_POST["tipopvp"];
        $fechae = date("Y-m-d h:i:s");
        $descto = $_POST["descto"];
        $escredito = $_POST["escredito"];
        $limitecred = $_POST["limitecred"];
        $diascred = $_POST["diascred"];
        $estoleran = $_POST["estoleran"];
        $diastole = $_POST["diastole"];
        $pais = '1';
        $estado = $_POST["estado"];
        $ciudad = $_POST["ciudad"];
        /*$descorder=1234;*/



        /*DATOS CLIENTE 2*/
        /*INSERT SACLIE_01 "CASO AJ"*/
        $codclie = $_POST["codclie"];
        $clasificacion = $_POST["clasificacion"];
        $diasvisita = $_POST["diasvisita"];
        $municipio = $_POST["municipio"];
        $codnestle = $_POST["codnestle"];
        $ruc = $_POST["ruc"];
        $ruta_alternativa_2 = $_POST["ruta_alternativa_2"];
        $latitud = $_POST["latitud"];
        $longitud = $_POST["longitud"];


        /*$peso_max_vehiculo = $vehiculo->get_vehiculo_por_id($_POST["id"]);

        $output["capacidad"] = $peso_max_vehiculo[0]["Capacidad"];*/
        $output["cubicajeMax"] = $peso_max_vehiculo[0]["Volumen"];

        echo json_encode($output);

        break;


    case "listar":

        $datos = $relacion->get_todos_los_clientes();

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        foreach ($datos as $row){

            $sub_array = array();

            $sub_array[] = $row["codclie"];
            $sub_array[] = $row["descrip"];
            $sub_array[] = $row["id3"];
            $sub_array[] = number_format($row['saldo'], 2, ",", ".");
            $sub_array[] = '<div class="col text-center"></button>'." ".'<button type="button" onClick="modalMostrarDocumentoEnDespacho(\''.$row["codclie"].'\');"  id="'.$row["codclie"].'" class="btn btn-info btn-sm update">Editar</button>'." ".'<button type="button" onClick="modalEliminarDocumentoEnDespacho(\''.$row["codclie"].'\');"  id="'.$row["codclie"].'" class="btn btn-info btn-sm ver_detalles">Ver Detalles</button></div>';

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


    case "obtener_opcion_para_juridico_o_natural":

        $tipo = $_POST["tipo"];

        if ($tipo != ""){
            if ($tipo == "0"){
                $output["descrip"] = '<label>Razón Social *</label>
                                      <input type="text" class="form-control input-sm" minlength="3" maxlength="60" id="descrip" name="descrip" placeholder="razón social" required>
                                      <br />';
                $output["ruc"] =  '<label>Ruc</label>
                                    <input type="text" class="form-control input-sm" maxlength="40" id="ruc" name="ruc" placeholder="ruc">
                                    <br />';
                $output["codclie"] = 'indique el RIF Ejemplo J311768773';
                $output["rif"] = 'RIF Ejemplo J311768773';
            }elseif ($tipo == "1"){
                $output["descrip"] = '<label>Nombres y Apellidos *</label>
                                      <div class="form-group row">
                                          <div class="col-sm-6">
                                              <input type="text" class="form-control" id="name1" name="name1" minlength="3" maxlength="15" placeholder="Primer Nombre" required="">
                                          </div>
                                          <div class="col-sm-6">
                                              <input type="text" class="form-control" id="name2" name="name2" minlength="3" maxlength="15" placeholder="Segundo Nombre">
                                          </div>
                                      </div>
                                      <div class="form-group row">
                                          <div class="col-sm-6">
                                              <input type="text" class="form-control" id="ape1" name="ape1" minlength="3" maxlength="15" placeholder="Primer Apellido" required="">
                                          </div>
                                          <div class="col-sm-6">
                                              <input type="text" class="form-control" id="ape2" name="ape2" minlength="3" maxlength="15" placeholder="Segundo Apellido">
                                          </div>
                                      </div>';
                $output["ruc"] = '';
                $output["codclie"] = 'indique el RIF Ejemplo V175528004';
                $output["rif"] = 'Cedula o RIF Ejemplo V175528004';
            }
        }

        echo json_encode($output);

        break;
}