<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("despachosrelacion_modelo.php");
require_once("../despachos/despachos_modelo.php");

//INSTANCIAMOS EL MODELO
$relacion = new DespachosRelacion();
$despachos = new Despachos();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_RelacionDespachos":
        # DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = array();

        # consulta a la base de datos para obtener todos los despachos creados
        $datos = $relacion->getRelacionDespachos();
        if (ArraysHelpers::validate($datos)) {
            foreach ($datos as $key => $row) {
                # DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $sub_array[] = '<div class="col text-center"><a href="#" onclick="modalTipoReporte(\'' . $row["correlativo"] . '\');" class="nav-link">
                                    <i class="far fa-file-pdf fa-2x" style="color:red"></i>
                                </a></div>';
                $sub_array[] = str_pad($row["correlativo"], 8, 0, STR_PAD_LEFT)
                    . '<br><span class="right badge badge-secondary mt-1">' . date(FORMAT_DATE, strtotime($row["fechae"])) . '</span>';
                $sub_array[] = $row["nomper"];
                $sub_array[] = $row["cantDocumentos"];
                $sub_array[] = $row["destino"] . " - " . $row["NomperChofer"];
                $sub_array[] = '<div class="col text-center"><a href="#" onclick="" class="nav-link">
                                    <img src="../../public/build/images/bs.png" width="25" height="25" border="0" />
                                </a></div>';
                $sub_array[] = '<div class="col text-center">
                                    <button type="button" onClick="modalVerDetalleDespacho(\'' . $row["correlativo"] . '\');" id="' . $row["correlativo"] . '" class="btn btn-info btn-sm ver_detalles">Detalle</button>' . " " . '
                                    <button type="button" onClick="modalEditarDespachos(\'' . $row["correlativo"] . '\');"    id="' . $row["correlativo"] . '" class="btn btn-info btn-sm update">Editar</button>' . " " . '
                                    <button type="button" onClick="EliminarUnDespacho(\'' . $row["correlativo"] . '\');"      id="' . $row["correlativo"] . '" class="btn btn-danger btn-sm eliminar">Eliminar</button>
                                </div>';

                $data[] = $sub_array;
            }
        }

        # RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "sEcho" => 1, # INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), # ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), # ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);
        echo json_encode($output);
        break;

    case "buscar_despacho_por_correlativo":

        # obtenemos el valor enviado por ajax
        $correlativo = $_POST['correlativo'];

        # buscamos en la bd la cabecera del despacho
        $cabecera_despacho = $relacion->get_despacho_por_correlativo($correlativo);

        # validamos que la consulta de la cabecera tenga registro
        if (ArraysHelpers::validate($cabecera_despacho)) {

            $cabecera_despacho = ArraysHelpers::validateWithPos($cabecera_despacho, 0);

            # si tiene se asignan a variables de salida
            $output["correl"] = str_pad($cabecera_despacho['correlativo'], 8, 0, STR_PAD_LEFT);
            $output["destino"] = $cabecera_despacho["destino"] . " - " . $cabecera_despacho["NomperChofer"];
            $output["fechad"] = date(FORMAT_DATE, strtotime($cabecera_despacho['fechad']));
            $output["vehiculo"] = $cabecera_despacho['placa'] . " " . $cabecera_despacho['modelo'] . " " . $cabecera_despacho['capacidad'] . " Kg";
            $output["cantDocumentos"] = $cabecera_despacho['cantDocumentos'];
        }

        echo json_encode($output);
        break;

    case "buscar_destalle_despacho_por_correlativo":

        # obtenemos el valor enviado por ajax
        $correlativo = $_POST['correlativo'];

        # DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = array();

        # buscamos en la bd la el detalle
        $detalle_despacho = $relacion->get_detalle_despacho_por_correlativo($correlativo);
        if (ArraysHelpers::validate($detalle_despacho)) {
            foreach ($detalle_despacho as $key => $row) {

                $documento = array();
                switch ($row['tipofac']) {
                    case 'A':
                        $documento = $despachos->getFactura($row['numerod']);
                        break;
                    case 'C':
                        $documento = $despachos->getNotaDeEntrega($row['numerod']);
                        break;
                }
                $documento = ArraysHelpers::validateWithPos($documento, 0);

                # DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $tipoDocu = ($row['tipofac'] == 'A') ? 'Factura' : 'Nota de Entrega';
                $tipoBadge = ($row['tipofac'] == 'A') ? 'badge-primary' : 'badge-secondary';

                $sub_array[] = $row["numerod"] . '<br><span class="right badge ' . $tipoBadge . '">' . $tipoDocu . '</span>';
                $sub_array[] = $documento["codclie"];
                $sub_array[] = $documento["descrip"];
                $sub_array[] = date(FORMAT_DATETIME2, strtotime($documento["fechae"]));
                $sub_array[] = Strings::rdecimal($documento["total"], 2);
                $sub_array[] = '<div class="col text-center">
                                      <button type="button" onClick="modalMostrarDocumentoEnDespacho(\'' . $row["numerod"] . '\',\'' . $row["tipofac"] . '\',\'' . $correlativo . '\');"   id="' . $row["numerod"] . '" class="btn btn-info btn-sm update">Editar</button>' . " " . '
                                      <button type="button" onClick="modalEliminarDocumentoEnDespacho(\'' . $row["numerod"] . '\',\'' . $row["tipofac"] . '\',\'' . $correlativo . '\');"  id="' . $row["numerod"] . '" class="btn btn-danger btn-sm eliminar">Eliminar</button>
                                </div>';

                $data[] = $sub_array;
            }
        }

        # RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "sEcho" => 1, # INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), # ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), # ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data
        );
        echo json_encode($output);
        break;

    case "buscar_cabeceraDespacho_para_editar":

        # obtenemos el correlativo enviado por ajax
        $correlativo = $_POST['correlativo'];

        # consultamos el despacho y la lista de choferes y vehiculos
        $despacho = $relacion->get_despacho_por_correlativo($correlativo);
        $output["lista_choferes"] = Choferes::todos();
        $output["lista_vehiculos"] = Vehiculo::todos();

        if (ArraysHelpers::validate($despacho)) {
            $despacho = ArraysHelpers::validateWithPos($despacho, 0);

            # asignamos en una variable de salida los datos necesarios del despacho
            $output["destino"] = $despacho["destino"];
            $output["fecha"] = $despacho['fechad'];
            $output["chofer"] = $despacho["ID_Chofer"];
            $output["vehiculo"] = $despacho['ID_Vehiculo'];
        }

        echo json_encode($output);
        break;

    case "actualizar_cabeceraDespacho_para_editar":

        $actualizar_despacho = false;

        $fecha_ant = $destino_ant = $cedula_ant = $placa_ant = "";

        $correlativo = $_POST["correlativo"];
        $id_vehiculo = $_POST["vehiculo"];
        $destino = $_POST["destino"];
        $id_chofer = $_POST["chofer"];
        $fechad = $_POST["fechad"];

        //consultamos si existe el despacho en la bd
        $despacho = $relacion->get_despacho_por_correlativo($correlativo);
        //validamos si el despacho existe
        if (ArraysHelpers::validate($despacho)) {
            $despacho = ArraysHelpers::validateWithPos($despacho, 0);
            $id_vehiculo_ant = $despacho['ID_Vehiculo'];
            $destino_ant = $despacho['Destino'];
            $id_chofer_ant = $despacho['ID_Chofer'];
            $fechad_ant = $despacho['fechad'];

            # datos anterior
            $vehiculo_ant = ArraysHelpers::validateWithPos(Vehiculo::getById($id_vehiculo_ant), 0);
            $chofer_ant = ArraysHelpers::validateWithPos(Choferes::getByDni($id_chofer_ant), 0);
            # datos nuevo
            $vehiculo = ArraysHelpers::validateWithPos(Vehiculo::getById($id_vehiculo), 0);
            $chofer = ArraysHelpers::validateWithPos(Choferes::getByDni($id_chofer), 0);

            # actualizar despacho
            $actualizar_despacho = $despachos->updateDespacho(
                $correlativo, $destino, $id_chofer, $id_vehiculo, $fechad
            );

            /**  enviar correo: despachos_edita_2 **/
            if ($actualizar_despacho) {
                # preparamos los datos a enviar
                $dataEmail = EmailData::DataEditarChoferesyDestinoDeDespacho(
                    array(
                        'usuario' => $_SESSION['login'],
                        'correl_despacho' => $correlativo,
                        'vehiculo_ant' => $vehiculo_ant['placa'] . " " . $vehiculo_ant['modelo'] . " " . $vehiculo_ant['capacidad'] . "Kg",
                        'destino_ant' => $destino_ant,
                        'chofer_ant' => $chofer_ant['Nomper'],
                        'fechad_ant' => $fechad_ant,
                        'vehiculo' => $vehiculo['placa'] . " " . $vehiculo['modelo'] . " " . $vehiculo['capacidad'] . "Kg",
                        'destino' => $destino,
                        'chofer' => $chofer['Nomper'],
                        'fechad' => $fechad,
                    )
                );

                # enviar correo
                $status_send = Email::send_email(
                    $dataEmail['title'],
                    $dataEmail['body'],
                    $dataEmail['recipients'],
                );
            }
        }

        ($actualizar_despacho) ? ($output["mensaje"] = "ACTUALIZADO CORRECTAMENTE") : ($output["mensaje"] = "ERROR");

        echo json_encode($output);
        break;

    case "actualizar_factura_en_despacho":

        $actualizar_documento = false;

        $correlativo = $_POST["correlativo"];
        $documento_nuevo = $_POST["documento_nuevo"];
        $documento_viejo = $_POST["documento_viejo"];

        //si no son iguales el documento ingresado al original
        if (!hash_equals($documento_nuevo, $documento_viejo)) {
            //consultamos si la factura existe en la bd
            $existe_factura = $despachos->getFactura($documento_nuevo);

            //validamos si la factura existe
            if (ArraysHelpers::validate($existe_factura)) {
                //consultamos si la factura existe en un despacho
                $existe_en_despacho = $despachos->getExisteFacturaEnDespachos($documento_nuevo);

                //validamos si el documento ingresado no exista en otro despacho
                if (count($existe_en_despacho) == 0) {
                    $factura_estado_1 = $relacion->get_factura_por_correlativo($correlativo);

                    if (count($factura_estado_1) == 0) {
                        //si cumple con todas las condiciones ACTUALIZA la factura en un despacho en especifico
                        $actualizar_documento = $despachos->updateDetalleDespacho($correlativo, $documento_nuevo, $documento_viejo);

                        $despacho = $relacion->get_despacho_por_correlativo($correlativo);

                        /**  enviar correo: despachos_edita_3 **/
                        if ($actualizar_documento) {
                            # preparamos los datos a enviar
                            $dataEmail = EmailData::DataDespachoEditarDocumento(
                                array(
                                    'usuario' => $_SESSION['login'],
                                    'correl_despacho' => $correlativo,
                                    'nroplanilla' => '0', #PENDIENTE
                                    'destino' => $despacho[0]['Destino'],
                                    'chofer' => $despacho[0]['NomperChofer'],
                                    'doc_viejo' => $documento_viejo,
                                    'doc_nuevo' => $documento_nuevo,
                                )
                            );

                            # enviar correo
                            $status_send = Email::send_email(
                                $dataEmail['title'],
                                $dataEmail['body'],
                                $dataEmail['recipients'],
                            );
                        }

                    }

                    //verificamos que se haya realizado la actualizacion correctamente y devolvemos el mensaje
                    ($actualizar_documento) ? ($output["mensaje"] = "ACTUALIZADO CORRECTAMENTE") : ($output["mensaje"] = "ERROR AL ACTUALIZAR");

                } else {
                    ($output["mensaje"] = 'ATENCION! el numero de documento: ' . $documento_nuevo . ', ya fue despachado');
                }

            } else {
                ($output["mensaje"] = 'ATENCION! EL numero de documento: ' . $documento_nuevo . ', no existe en el sistema');
            }

        } else {
            ($output["mensaje"] = 'ATENCION! Por favor ingrese un documento diferente');
        }

        echo json_encode($output);

        break;

    case "eliminar_factura_en_despacho":

        $eliminar_documento = false;

        $correlativo = $_POST["correlativo"];
        $nro_documento = $_POST["nro_documento"];

        //consultamos si existe el despacho en la bd
        $despacho = $relacion->get_despacho_por_correlativo($correlativo);
        //validamos si el despacho existe
        if (ArraysHelpers::validate($despacho)) {
            //consultamos si la factura existe en un despacho
            $existe_en_despacho = $despachos->getExisteFacturaEnDespachos($nro_documento);

            if (ArraysHelpers::validate($existe_en_despacho)) {
                //eliminamos de un despacho en especifico
                $eliminar_documento = $despachos->deleteDetalleDespacho(
                    $correlativo, $nro_documento
                );

                /**  enviar correo: despachos_elimina_fact **/
                if ($eliminar_documento) {
                    # preparamos los datos a enviar
                    $dataEmail = EmailData::DataEliminarDocumentoDespacho(
                        array(
                            'usuario' => $_SESSION['login'],
                            'correl_despacho' => $correlativo,
                            'nroplanilla' => '0', #PENDIENTE
                            'doc' => $nro_documento,
                        )
                    );

                    # enviar correo
                    $status_send = Email::send_email(
                        $dataEmail['title'],
                        $dataEmail['body'],
                        $dataEmail['recipients'],
                    );
                }
            }
        }

        //verificamos que se haya realizado la eliminacion del documento correctamente y devolvemos el mensaje
        ($eliminar_documento) ? $output["mensaje"] = 'ELIMINADO EXITOSAMENTE' : $output["mensaje"] = 'ERROR AL ELIMINAR';

        echo json_encode($output);
        break;

    case "agregar_documento_en_despacho":

        $insertar_documento = false;

        $correlativo = $_POST["correlativo"];
        $nro_documento = $_POST["documento_agregar"];
        $tipodoc = $_POST["tipodoc"];

        # tipodoc 'f' es Factura
        if ($tipodoc == 'f')
        {
            # consultamos si la factura existe en la bd
            $existe_factura = $despachos->getFactura($nro_documento);
            if (ArraysHelpers::validate($existe_factura))
            {
                # validamos si existe la factura (tipo A) en despacho
                $existe_fact_en_despachos = $despachos->getExisteDocumentoEnDespachos($nro_documento, 'A');
                if(!ArraysHelpers::validate($existe_fact_en_despachos))
                {
                    # si no existe el numerod en despacho
                    # validamos el peso (tara) de la factura
                    $arr_tara = $despachos->getCubicajeYPesoTotalporFactura($nro_documento);
                    if (ArraysHelpers::validate($arr_tara)) {
                        $peso = $cubicaje = 0;
                        foreach ($arr_tara as $tara) {
                            # valida que si es bulto (0) o paquete (1)
                            if ($tara['unidad'] == 0) {
                                $peso += ($tara['tara'] * $tara['cantidad']);
                            } else {
                                $peso += (($tara['tara'] / $tara['paquetes']) * $tara['cantidad']);
                            }
                            $cubicaje += $tara['cubicaje'];
                        }

                        # valida el peso y obtinene el porcentaje
                        $response = DespachosHelpers::validateWeightAndCubicCapacity(
                            array(
                                'peso'      => $peso,
                                'peso_acum' => $peso_acum,
                                'peso_max'  => $peso_max,
                                'cubicaje_acum' => $cubicaje_acum,
                                'cubicaje'      => $cubicaje,
                                'cubicaje_max'  => $cubicaje_max,
                            )
                        );
                        $output = $response;

                        # verifica si el peso esta dentro del rango
                        if ($response['cond'] == true)
                        {
                            $factura_estado_1 = $relacion->get_documentos_por_correlativo($correlativo);
                            if (count($factura_estado_1) == 0) {
                                # si cumple con todas las condiciones INSERTA la factura en un despacho en especifico
                                $insertar_documento = $despachos->insertarDetalleDespacho(
                                    $correlativo, $nro_documento, 'A', '1'
                                );

                                $despacho = $relacion->get_despacho_por_correlativo($correlativo);

                                /**  enviar correo: despachos_edita_4 **/
                                if ($insertar_documento) {
                                    # preparamos los datos a enviar
                                    $dataEmail = EmailData::DataDespachoAgregarDocumento(
                                        array(
                                            'usuario' => $_SESSION['login'],
                                            'correl_despacho' => $correlativo,
                                            'nroplanilla' => '0', #PENDIENTE
                                            'destino' => $despacho[0]['Destino'],
                                            'chofer' => $despacho[0]['NomperChofer'],
                                            'doc' => $nro_documento,
                                        )
                                    );

                                    # enviar correo
                                    $status_send = Email::send_email(
                                        $dataEmail['title'],
                                        $dataEmail['body'],
                                        $dataEmail['recipients'],
                                    );
                                }
                            }

                            //verificamos que se haya realizado la insercion correctamente y devolvemos el mensaje
                            ($insertar_documento)
                                ? ($output["mensaje"] = "INSERTADO CORRECTAMENTE ")
                                : ($output["mensaje"] = "ERROR AL INSERTAR");





                        } else {
                            $output["mensaje"] = ("El vehículo excede el límite de peso!");
                        }
                    } else {
                        $output["mensaje"] = ("Error al evaluar el peso y cubicaje");
                    }
                } else {
                    $output["mensaje"] = ("El Número de Factura: $documento_nuevo Ya Fue Despachado");
                }
            } else {
                $output["mensaje"] = "El Número de Factura $documento_nuevo No Existe en Sistema";
            }
        }
        # tipodoc 'n' es Nota de Entrega
        elseif ($tipodoc == 'n') {

        }

        echo json_encode($output);
        break;

    case "eliminar_un_despacho":

        $eliminar_despacho = false;
        $correlativo = $_POST["correlativo"];

        $existe_despacho = $despachos->get_despacho_por_id($correlativo);
        if (ArraysHelpers::validate($existe_despacho)) {
            //eliminamos de un despacho en especifico
            $eliminar_despacho = $despachos->deleteDespacho($correlativo);

            /**  enviar correo: despachos_elimina **/
            if ($eliminar_despacho) {
                # preparamos los datos a enviar
                $dataEmail = EmailData::DataEliminarDespacho(
                    array(
                        'usuario' => $_SESSION['login'],
                        'correl_despacho' => $correlativo,
                        'nroplanilla' => '0', #PENDIENTE
                    )
                );

                # enviar correo
                $status_send = Email::send_email(
                    $dataEmail['title'],
                    $dataEmail['body'],
                    $dataEmail['recipients'],
                );
            }
        }

        //verificamos que se haya realizado la eliminacion del documento correctamente y devolvemos el mensaje
        if ($eliminar_despacho) {
            $output["mensaje"] = 'ELIMINADO EXITOSAMENTE';
            $output["icono"] = "success";
        } else {
            $output["mensaje"] = 'ERROR AL ELIMINAR';
            $output["icono"] = "error";
        }

        echo json_encode($output);
        break;

    case "listar_productos_de_un_despacho":

        # correlativo
        $correlativo = $_POST["correlativo"];

        # obtenemos los registros de los productos en dichos documentos
        $productosDespacho = array();
        $datos_f = $despachos->getProductosDespachoCreadoEnFacturas($correlativo);
        $datos_n = $despachos->getProductosDespachoCreadoEnNotaDeEntrega($correlativo);

        foreach (array($datos_f, $datos_n) as $dato) {
            foreach ($dato as $row) {
                $arr = array_map(function ($arr) { return $arr['coditem']; }, $productosDespacho);

                if (!in_array($row['coditem'], $arr)) {
                    #no existe en el array
                    $productosDespacho[] = $row;
                } else {
                    # si existe en el array
                    $pos = array_search($row['coditem'], $arr);
                    $productosDespacho[$pos]['bultos'] += intval($row['bultos']);
                    $productosDespacho[$pos]['paquetes'] += intval($row['paquetes']);
                }
            }
        }

        # DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        $total_bultos = 0;
        $total_paq = 0;
        foreach ($productosDespacho as $row) {

            # DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            # REALIZAMOS PROCESOS DE CALCULO
            $bultos = 0;
            $paq = 0;
            if ($row["bultos"] > 0){
                $bultos = $row["bultos"];
            }
            if ($row["paquetes"] > 0){
                $paq = $row["paquetes"];
            }

            if ($row["esempaque"] != 0){
                if ($row["paquetes"] > $row["cantempaq"]){

                    if ($row["cantempaq"] != 0) {
                        $bultos_total = $row["paquetes"] / $row["cantempaq"];
                    }else{
                        $bultos_total = 0;
                    }
                    $decimales = explode(".",$bultos_total);
                    $bultos_deci = $bultos_total - $decimales[0];
                    $paq = $bultos_deci * $row["cantempaq"];
                    $bultos = $decimales[0] + $bultos;
                }
            }
            $total_bultos += $bultos;
            $total_paq += $paq;

            # agregamos al sub array
            $sub_array[] = $row["coditem"];
            $sub_array[] = $row["descrip"];
            $sub_array[] = round($bultos);
            $sub_array[] = round($paq);

            # agregamos un registro al array principal
            $data[] = $sub_array;
        }

        # totalizado que se imprimira en la parte inferior de la tabla
        $output["total_bultos"] = $total_bultos;
        $output["total_paq"] = $total_paq;

        # RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output['tabla'] = array(
            "sEcho" => 1, # INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), # ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), # ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);

        echo json_encode($output);
        break;

}