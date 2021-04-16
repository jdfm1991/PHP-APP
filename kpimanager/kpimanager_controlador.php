<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("kpimanager_modelo.php");

//INSTANCIAMOS EL MODELO
$kpiManager = new KpiManager();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "activarydesactivar":
        $codvend = $_POST["id"];
        $activo  = $_POST["est"];
        //consultamos si existe el registro
        $datos = $kpiManager->get_datos_edv($codvend);
        //valida el id del cliente
        if (is_array($datos) == true and count($datos) > 0) {
            //si esta activo(1) lo situamos cero(0), y viceversa
            ($activo == "0") ? $activo = 1 : $activo = 0;
            //edita el estado
            $estado = $kpiManager->editar_estado_edv($codvend, $activo);
            //evalua que se realizara el query
            ($estado) ? $output["mensaje"] = "Actualizacion realizada Exitosamente" : $output["mensaje"] = "Error al Actualizar";
        }

        echo json_encode($output);
        break;


    case "listar":
        $edv = $_POST['edv'];
        $datos = $kpiManager->get_datos_edv($edv);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        foreach ($datos as $row){

            $sub_array = array();

            //ESTADO
            $est = '';
            $atrib = "btn btn-success btn-sm estado";
            switch ($row["activo"]){
                case 0:
                    $est = 'INACTIVO';
                    $atrib = "btn btn-warning btn-sm estado";
                    break;
                case 1:
                    $est = 'ACTIVO';
                    break;
            }

            $coordinador = ($row["activo"]==1 and empty($row["Coordinador"])) ? '<br><span class="right badge badge-primary">Coordinador no asignado</span>' : '';

            $sub_array[] = $row["CodVend"];
            $sub_array[] = $row["Descrip"] . $coordinador;
            $sub_array[] = $row["clase"];
            $sub_array[] = !empty($row["ubicacion"]) ? $row["ubicacion"] : "-";
            $sub_array[] = '<div class="col text-center">
                                <button type="button" onClick="cambiarEstado(\''.$row["CodVend"].'\',\''.$row["activo"].'\');" name="estado" id="' . $row["CodVend"] . '" class="' . $atrib . '">' . $est . '</button>' . " " . '
                                <button type="button" onClick="mostrar(\''. $row["CodVend"] .'\');"  id="' . $row["CodVend"] . '" class="btn btn-info btn-sm update">Editar</button>
                            </div>';

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


    case "mostrar":

        $codvend = $_POST["edv"];

        if($codvend != "-") {
            $row = $kpiManager->get_datos_edv($codvend);
            $clientes = $kpiManager->get_clientesPorEdv($codvend);

            $obj_dropsize = (count($clientes)>0 and (!empty($row[0]['Frecuencia']) and $row[0]['Frecuencia']>0))
                                                        ? ($row[0]['ObjVentasBs'])/count($clientes)*$row[0]['Frecuencia'] : 0;

            $tiempo_estimado_despacho = !empty($row[0]['Tiempo_Estimado_Despacho']) ? trim($row[0]['Tiempo_Estimado_Despacho']) : 0;
            $obj_ventas_kg      = !empty($row[0]['ObjVentasKg']) ? trim($row[0]['ObjVentasKg']) : 0;
            $obj_ventas_bul     = !empty($row[0]['ObjVentasBu']) ? trim($row[0]['ObjVentasBu']) : 0;
            $obj_ventas_und     = !empty($row[0]['ObjVentasUn']) ? trim($row[0]['ObjVentasUn']) : 0;
            $obj_logro_especial = !empty($row[0]['ObjLogEspec']) ? trim($row[0]['ObjLogEspec']) : 0;
            $obj_ventas_divisas = !empty($row[0]['ObjVentasBs']) ? trim($row[0]['ObjVentasBs']) : 0;

            $output = array(
                'codvend'        => $row[0]['CodVend'],
                'obj_ventas_kg'  => str_replace(',', '', number_format($obj_ventas_kg)),
                'nombre'         => trim($row[0]['apenom']),
                'obj_ventas_bul' => str_replace(',', '', number_format($obj_ventas_bul)),
                'cedula'         => trim($row[0]['cedula']),
                'obj_ventas_und' => str_replace(',', '', number_format($obj_ventas_und)),
                'ubicacion'      => trim($row[0]['ubicacion']),
                'obj_dropsize'   => str_replace(',', '', number_format($obj_dropsize)),
                'clase'          => trim($row[0]['clase']),
                'obj_captar_clientes' => $row[0]['ObjCaptar'],
                'coordinador'         => trim($row[0]['Coordinador']),
                'obj_especial'        =>str_replace(',', '', number_format(trim($row[0]['ObjEspecial']))),
                'deposito'            => '01',
                'obj_logro_especial'  => number_format($obj_logro_especial),
                'frecuencia'          => trim($row[0]['Frecuencia']),
                'tiempo_estimado_despacho'  => $tiempo_estimado_despacho,
                'obj_ventas_divisas'        => str_replace(',', '', number_format($obj_ventas_divisas)),
                'objetivo_kpi'      => str_replace(',', '', number_format(trim($row[0]['Requerido_Bult_Und']))),
                'ava'               => trim($row[0]['ava']),
                'ava_fotos'         => trim($row[0]['ava_fotos']),
                'lista_obj_kpi'     => $kpiManager->get_objetivos_kpi(),
                'lista_clases_kpi'  => $kpiManager->get_clases_edv(),
            );
        }

        echo json_encode($output);

        break;


    case "guardar":
        $escredito = $_POST["escredito"];
        //eliminamos los puntos, y cambia la coma por punto
        $limitecred = str_replace(".", "", str_replace(",", ".", $_POST["LimiteCred"]));
        $diascred = $_POST["diascred"];
        $estoleran = $_POST["estoleran"];
        $diastole = $_POST["diasTole"];
        $descto = str_replace(".", "", str_replace(",", ".", $_POST["descto"]));
        $observacion = $_POST["observa"]; //saclie_ext


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
        if($saclie){
            $output["mensaje"] = "Cliente $codclie Actualizado con Exito";
            $output["icono"] = "success";
        } else {
            //en caso de error mostrara uno de los mensajes asignados
            $output["icono"] = "error";
        }

        echo json_encode($output);
        break;
}
