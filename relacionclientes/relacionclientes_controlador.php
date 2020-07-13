<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO
require_once("relacionclientes_modelo.php");

//INSTANCIAMOS EL MODELO
$relacion = new RelacionClientes();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "activarydesactivar":

        $codclie = $_POST["codclie"];
        $activo = $_POST["est"];
        //consultamos el registro del cliente
        $datos = $relacion->get_cliente_por_id($codclie);
        //valida el id del cliente
        if (is_array($datos) == true and count($datos) > 0) {
            //si esta activo(1) lo situamos cero(0), y viceversa
            ($activo=="0") ? $activo = 1 : $activo = 0;
            //edita el estado del cliente
            $estado = $relacion->editar_estado($codclie, $activo);
            //evalua que se realizara el query
            ($estado) ? $output["mensaje"] = "Actualizacion realizada Exitosamente" : $output["mensaje"] = "Error al Actualizar";
        }

        echo json_encode($output);

        break;

    case "guardaryeditar":

        //inicializamos la variables de control principales
        $saclie = $saclie_ext = false;

        /** DATOS PRINCIPALES **/
        $tipo_cliente = $_POST["tipo_cliente"];
        $codclie = $_POST["codclie"];
        if($tipo_cliente == "0") { //juridico
            $descrip = $_POST["descrip"];
            $ruc = $_POST["ruc"]; //saclie_ext
            $descorder = "";
        }
        elseif($tipo_cliente == "1")
        { //natural
            $nomb1 = $_POST['name1'];
            $nomb2 = $_POST['name2'];
            $ape1 = $_POST['ape1'];
            $ape2 = $_POST['ape2'];
            $descrip = "$nomb1 $nomb2 $ape1 $ape2";
            $ruc = "";
            $descorder = 1234;
        }
        $id3 = $_POST["id3"];
        $clase = $_POST["clase"];
        $represent = $_POST["represent"];
        $direc1 = $_POST["direc1"];
        $direc2 = $_POST["direc2"];
        $pais = '1';
        $estado = $_POST["estado"];
        $ciudad = $_POST["ciudad"];
        $municipio = $_POST["municipio"]; //saclie_ext
        $email = $_POST["email"];
        $telef = $_POST["telef"];
        $movil = $_POST["movil"];
        $activo = $_POST["activo"];

        /** DATOS ADICIONALES **/
        $codzona = $_POST["codzona"];
        $codvend = $_POST["codvend"];
        $tipocli = $_POST["tipocli"];
        $tipopvp = $_POST["tipopvp"];
        $diasvisita = $_POST["diasvisita"]; //saclie_ext
//        $ruc = isset($_POST["ruc"]);
        $latitud = $_POST["latitud"]; //saclie_ext
        $longitud = $_POST["longitud"]; //saclie_ext
        $codnestle = $_POST["codnestle"]; //saclie_ext

        /** DATOS FINANCIEROS **/
        $escredito = $_POST["escredito"];
        //eliminamos los puntos, y cambia la coma por punto
        $limitecred = str_replace(".", "", str_replace(",", ".", $_POST["LimiteCred"]));
        $diascred = $_POST["diascred"];
        $estoleran = $_POST["estoleran"];
        $diastole = $_POST["diasTole"];
        $fecha_creacion = date("Y-m-d h:i:s");
        $descto = str_replace(".", "", str_replace(",", ".", $_POST["descto"]));
        $observacion = $_POST["observa"]; //saclie_ext


        /* consulta si la variable id_cliente llego vacia para realizar
           operaciones de creacion o actualizacion de cliente */
        if (empty($_POST["id_cliente"])) {

            /*verificamos si existe el codigo o el rif del cliente en la base de datos, si ya existe un registro entonces no se registra el cliente*/
            $datos = $relacion->get_cliente_por_codigo_o_rif($codclie, $id3);

            if (is_array($datos) == true and count($datos) == 0) {

                //no existe registro del cliente, por lo tanto hacemos el registro
                $saclie = $relacion->registrar_cliente($tipo_cliente, $codclie, $descrip, $descorder, $id3, $clase, $represent, $direc1, $direc2, $pais, $estado, $ciudad, $email, $telef, $movil, $activo, $codzona, $codvend, $tipocli, $tipopvp, $escredito, $limitecred, $diascred, $estoleran, $diastole, $fecha_creacion, $descto);
                if($saclie){
                    //si registro bien en saclie, inserta los datos en saclie_ext
                    $saclie_ext = $relacion->registrar_cliente_ext($codclie, $municipio, $diasvisita, $ruc, $latitud, $longitud, $codnestle, $observacion);

                    //evalua si se inserto bien en saclie_ext.
                    if(!$saclie_ext)
                        $output["mensaje"] = "Error al insertar en saclie_ext $codclie";

                } else {
                    $output["mensaje"] = "Error al insertar cliente";
                }
            } else {
                $output["mensaje"] = "El codigo o el rif ya existe";
            }

            //mensaje
            if($saclie && $saclie_ext){
                $output["mensaje"] = "Cliente $codclie Creado con Exito";
                $output["icono"] = "success";
            } else {
                //en caso de error mostrara uno de los mensajes asignados
                $output["icono"] = "error";
            }
        } else {
            /*si ya existe entonces actualizamos el cliente*/
            $saclie = $relacion->actualizar_cliente($codclie, $descrip, $descorder, $id3, $clase, $represent, $direc1, $direc2, $pais, $estado, $ciudad, $email, $telef, $movil, $activo, $codzona, $codvend, $tipocli, $tipopvp, $escredito, $limitecred, $diascred, $estoleran, $diastole, $descto);

            //si actualizo bien los datos del cliente en saclie, continua con saclie_ext
            if($saclie){
                //evalua si existe un registro en la tabla saclie_ext, si existe actualizamos los datos, si no existe crea un nuevo registro.
                $datos = $relacion->get_cliente_Ext_por_codigo($codclie);

                if (is_array($datos) == true and count($datos) == 0) {
                    //no existe registro saclie_ext del cliente, lo inserta
                    $saclie_ext = $relacion->registrar_cliente_ext($codclie, $municipio, $diasvisita, $ruc, $latitud, $longitud, $codnestle, $observacion);
                } else {
                    //como existe un registro en la base de datos, lo Actualiza
                    $saclie_ext = $relacion->actualizar_cliente_ext($codclie, $municipio, $diasvisita, $ruc, $latitud, $longitud, $codnestle, $observacion);
                }
                //evalua si se actualizo o inserto bien los datos.
                if(!$saclie_ext)
                    $output["mensaje"] = "Error al insertar o actualizar Saclie_ext $codclie";

            } else {
                $output["mensaje"] = "Error al actualizar cliente";
            }

            //mensaje
            if($saclie && $saclie_ext){
                $output["mensaje"] = "Cliente $codclie Actualizado con Exito";
                $output["icono"] = "success";
            } else {
                //en caso de error mostrara uno de los mensajes asignados
                $output["icono"] = "error";
            }
        }

        echo json_encode($output);

        break;


    case "listar":

        $datos = $relacion->get_todos_los_clientes();

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        foreach ($datos as $row){

            $sub_array = array();

            //ESTADO
            $est = '';
            $atrib = "btn btn-success btn-sm estado";
            switch ($row["idactivo"]){
                case 0:
                    $est = 'INACTIVO';
                    $atrib = "btn btn-warning btn-sm estado";
                    break;
                case 1:
                    $est = 'ACTIVO';
                    break;
            }

            $sub_array[] = $row["codclie"];
            $sub_array[] = $row["descrip"];
            $sub_array[] = $row["id3"];
            $sub_array[] = number_format($row['saldo'], 2, ",", ".");
            $sub_array[] = '<div class="col text-center"><button type="button" onClick="cambiarEstado(\''.$row["codclie"].'\',\''.$row["idactivo"].'\');" name="estado" id="' . $row["codclie"] . '" class="' . $atrib . '">' . $est . '</button>' . " " . '</button>'." ".'<button type="button" onClick="mostrar(\''.$row["codclie"].'\',\''.$row["idtid3"].'\');"  id="'.$row["codclie"].'" class="btn btn-info btn-sm update">Editar</button>'." ".'<button type="button" onClick="modalEliminarDocumentoEnDespacho(\''.$row["codclie"].'\');"  id="'.$row["codclie"].'" class="btn btn-info btn-sm ver_detalles">Ver Detalles</button></div>';

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

    case "listar_detalle_cliente":

        $codclie = $_POST["codclie"];

        $lista_estados = $relacion->get_estados();
        $lista_zonas = $relacion->get_zona();
        $lista_vendedores = $relacion->get_Edv();
        $lista_codnestle = $relacion->get_Cnestle();

        if($codclie != "") {
            $cliente = $relacion->get_cliente_por_id($codclie);

            $output["tipoid3"] = $cliente[0]['idtid3'];
            $output["codclie"] = $cliente[0]['codigo'];
            if($cliente[0]['idtid3'] == "0")
            { //juridico
                $output["descrip"] = $cliente[0]['descrip'];
                //si existe ruc en saclie_ext, llenar, sino vacio
                (isset($cliente[0]['ruc'])) ? $output["ruc"] = $cliente[0]['ruc'] : $output["ruc"] = "";
            }
            elseif($cliente[0]['idtid3'] == "1")
            { //natural
                $descrip = explode(" ", $cliente[0]['descrip']);
                (isset($descrip[0])) ? $output["name1"] = $descrip[0] : $output["name1"] = "";
                (isset($descrip[1])) ? $output["name2"] = $descrip[1] : $output["name2"] = "";
                (isset($descrip[2])) ? $output["ape1"]  = $descrip[2] : $output["ape1"] = "";
                (isset($descrip[3])) ? $output["ape2"]  = $descrip[3] : $output["ape2"] = "";
            }
            $output["id3"] = $cliente[0]['id3'];
            $output["clase"] = $cliente[0]['clase'];
            $output["represent"] = $cliente[0]['represent'];
            $output["direc1"] = $cliente[0]['direc1'];
            $output["direc2"] = $cliente[0]['direc2'];
            $output["idestado"] = $cliente[0]['idestado'];
            $output["idciudad"] = $cliente[0]['idciudad'];
            //si existe minicipio en saclie_ext, llenar, sino vacio
            (isset($cliente[0]['municipio'])) ? $output["municipio"] = $cliente[0]['municipio'] : $output["municipio"] = "";
            $output["email"] = $cliente[0]['email'];
            $output["telef"] = $cliente[0]['telef'];
            $output["movil"] = $cliente[0]['movil'];
            $output["idactivo"] = $cliente[0]['idactivo'];
            $output["codzona"] = $cliente[0]['idzona'];
            $output["codvend"] = $cliente[0]['idvend'];
            $output["tipocli"] = $cliente[0]['idtcli'];
            $output["idtpvp"] = $cliente[0]['idtpvp'];
            //si existe dvisitas en saclie_ext, llenar, sino vacio
            (isset($cliente[0]['dvisitas'])) ? $output["diasvisita"] = $cliente[0]['dvisitas'] : $output["diasvisita"] = "";
            //si existe latitud en saclie_ext, llenar, sino vacio
            (isset($cliente[0]['latitud'])) ? $output["latitud"] = $cliente[0]['latitud'] : $output["latitud"] = "";
            //si existe longitud en saclie_ext, llenar, sino vacio
            (isset($cliente[0]['longitud'])) ? $output["longitud"] = $cliente[0]['longitud'] : $output["longitud"] = "";
            //si existe idnestle en saclie_ext, llenar, sino vacio
            (isset($cliente[0]['idnestle'])) ? $output["idnestle"] = $cliente[0]['idnestle'] : $output["idnestle"] = "";
            $output["escredito"] = $cliente[0]['credito'];
            $output["LimiteCred"] = number_format($cliente[0]['lcred'], 2, ",", ".");
            $output["diascred"] = $cliente[0]['dcred'];
            $output["estoleran"] = $cliente[0]['toleran'];
            $output["diasTole"] = $cliente[0]['dtoleran'];
            $output["descto"] = number_format($cliente[0]['descto'], 2, ",", ".");
            //si existe observa en saclie_ext, llenar, sino vacio
            (isset($cliente[0]['observa'])) ? $output["observa"] = $cliente[0]['observa'] : $output["observa"] = "";
        }

        //ESTADOS
        $output["estado"] = '<option name="" value="">Seleccione</option>';
        if(count($lista_estados) > 0) {
            foreach ($lista_estados as $estado)
                $output["estado"] .= '<option value="' . $estado['estado'] . '">' . $estado['descrip'] . '</option>';
        }

        //ZONAS
        $output["zona"] = '<option name="" value="">Seleccione</option>';
        if(count($lista_zonas) > 0) {
            foreach ($lista_zonas as $zona)
                $output["zona"] .= '<option value="' . $zona['codzona'] . '">' . $zona['descrip'] . '</option>';
        }

        //VENDEDORES
        $output["edv"] = '<option name="" value="">Seleccione</option>';
        if(count($lista_vendedores) > 0) {
            foreach ($lista_vendedores as $vendedor)
                $output["edv"] .= '<option value="' . $vendedor['codvend'] . '">' . $vendedor['descrip'] . '</option>';
        }

        //CODIGOS NESTLE
        $output["codnestle"] = '<option name="" value="">Seleccione</option>';
        if(count($lista_codnestle) > 0) {
            foreach ($lista_codnestle as $codnestle)
                $output["codnestle"] .= '<option value="' . $codnestle['codnestle'] . '">' . $codnestle['descrip'] . '</option>';
        }

        echo json_encode($output);

        break;


    case "listar_ciudades_por_idestado":

        $estado_selected = $_POST["idestado"];

        $lista_ciudades = $relacion->get_ciudades_por_estado($estado_selected);

        //CIUDADES
        $output["ciudades"] = '<option name="" value="">Seleccione</option>';
        if(count($lista_ciudades) > 0) {
            foreach ($lista_ciudades as $ciudad)
                $output["ciudades"] .= '<option value="' . $ciudad['ciudad'] . '">' . $ciudad['descrip'] . '</option>';
        }

        echo json_encode($output);

        break;
}