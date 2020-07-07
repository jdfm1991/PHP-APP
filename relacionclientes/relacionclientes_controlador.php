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

        //datos principales
        $tipo_cliente = isset($_POST["tipo_cliente"]); //tipo cliente
        $codclie = isset($_POST["codclie"]);
        $descrip = isset($_POST["descrip"]);
        $nomb1 = $_POST['nomb1'];
        $nomb2 = $_POST['nomb2'];
        $ape1 = $_POST['ape1'];
        $ape2 = $_POST['ape2'];
        $id3 = isset($_POST["id3"]);
        $clase = isset($_POST["clase"]);
        $represent = isset($_POST["represent"]);
        $direc1 = isset($_POST["direc1"]);
        $direc2 = isset($_POST["direc2"]);
        $pais = '1';
        $estado = isset($_POST["estado"]);
        $ciudad = isset($_POST["ciudad"]);
        $municipio = isset($_POST["municipio"]);
        $email = isset($_POST["email"]);
        $telef = isset($_POST["telef"]);
        $movil = isset($_POST["movil"]);
        $activo = isset($_POST["activo"]);

        //datos adicionales
        $codzona = isset($_POST["codzona"]);
        $codvend = isset($_POST["codvend"]);
        $tipocli = isset($_POST["tipocli"]);
        $tipopvp = isset($_POST["tipopvp"]);
        $diasvisita = isset($_POST["diasvisita"]);
        $ruc = isset($_POST["ruc"]);
        $latitud = isset($_POST["latitud"]);
        $longitud = isset($_POST["longitud"]);
        $codnestle = isset($_POST["codnestle"]);

        //datos financieros
        $escredito = isset($_POST["escredito"]);
        $limitecred = isset($_POST["LimiteCred"]);
        $diascred = isset($_POST["diascred"]);
        $estoleran = isset($_POST["estoleran"]);
        $diastole = isset($_POST["diasTole"]);
        $descto = isset($_POST["descto"]);
        $observa = isset($_POST["observa"]);


        $fechae = date("Y-m-d h:i:s");

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
            $sub_array[] = '<div class="col text-center"></button>'." ".'<button type="button" onClick="mostrar(\''.$row["codclie"].'\');"  id="'.$row["codclie"].'" class="btn btn-info btn-sm update">Editar</button>'." ".'<button type="button" onClick="modalEliminarDocumentoEnDespacho(\''.$row["codclie"].'\');"  id="'.$row["codclie"].'" class="btn btn-info btn-sm ver_detalles">Ver Detalles</button></div>';

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

    case "listar_estado_codzona_codvend_codnestle":

        $codclie = $_POST["codclie"];

        if($codclie != "")
            $cliente = $relacion->get_cliente_por_id($codclie);

        $lista_estados = $relacion->get_estados();
        $lista_zonas = $relacion->get_zona();
        $lista_vendedores = $relacion->get_Edv();
        $lista_codnestle = $relacion->get_Cnestle();

        //ESTADOS
        $output["estado"] = '<option name="" value="">Seleccione</option>';
        if(count($lista_estados) > 0) {
            foreach ($lista_estados as $estado)
            {
                if($codclie != "" && $estado["estado"] == $cliente[0]['idestado']) {
                    $output["estado"] .= '<option value="' . $estado['estado'] . '" selected>' . $estado['descrip'] . '</option>';
                } else {
                    $output["estado"] .= '<option value="' . $estado['estado'] . '">' . $estado['descrip'] . '</option>';
                }
            }

        }

        //ZONAS
        $output["zona"] = '<option name="" value="">Seleccione</option>';
        if(count($lista_zonas) > 0) {
            foreach ($lista_zonas as $zona)
            {
                if($codclie != "" && $zona["codzona"] == $cliente[0]['idzona']) {
                    $output["zona"] .= '<option value="' . $zona['codzona'] . '" selected>' . $zona['descrip'] . '</option>';
                } else {
                    $output["zona"] .= '<option value="' . $zona['codzona'] . '">' . $zona['descrip'] . '</option>';
                }
            }
        }

        //VENDEDORES
        $output["edv"] = '<option name="" value="">Seleccione</option>';
        if(count($lista_vendedores) > 0) {
            foreach ($lista_vendedores as $vendedor)
            {
                if($codclie != "" && $vendedor["codvend"] == $cliente[0]['idvend']) {
                    $output["edv"] .= '<option value="' . $vendedor['codvend'] . '" selected>' . $vendedor['descrip'] . '</option>';
                } else {
                    $output["edv"] .= '<option value="' . $vendedor['codvend'] . '">' . $vendedor['descrip'] . '</option>';
                }
            }

        }

        //CODIGOS NESTLE
        $output["codnestle"] = '<option name="" value="">Seleccione</option>';
        if(count($lista_codnestle) > 0) {
            foreach ($lista_codnestle as $codnestle)
            {
                if($codclie != "" &&   isset($cliente[0]['idnestle']) && $codnestle["codnestle"] == $cliente[0]['idnestle']) {
                    $output["codnestle"] .= '<option value="' . $codnestle['codnestle'] . '" selected>' . $codnestle['descrip'] . '</option>';
                } else {
                    $output["codnestle"] .= '<option value="' . $codnestle['codnestle'] . '">' . $codnestle['descrip'] . '</option>';
                }
            }
        }

        echo json_encode($output);

        break;


    case "listar_ciudad":

        $codclie = $_POST["codclie"];
        $estado_selected = $_POST["idestado"];

        $lista_ciudades = $relacion->get_ciudades_por_estado($estado_selected);

        //CIUDADES
        $output["ciudad"] = '<option name="" value="">Seleccione</option>';
        if(count($lista_ciudades) > 0) {
            foreach ($lista_ciudades as $ciudad)
            {
                if($codclie != "" && $ciudad["ciudad"] == $cliente[0]['idestado']) {
                    $output["ciudad"] .= '<option value="' . $ciudad['ciudad'] . '" selected>' . $ciudad['descrip'] . '</option>';
                } else {
                    $output["ciudad"] .= '<option value="' . $ciudad['ciudad'] . '">' . $ciudad['descrip'] . '</option>';
                }
            }

        }

        echo json_encode($output);

        break;
}