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

        //consulta a la base de datos para obtener todos los despachos creados
        $datos = $relacion->getRelacionDespachos();

        if(is_array($datos) == true and count($datos) > 0) {

            //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
            $data = Array();

            foreach ($datos as $row) {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $sub_array[] = '<div class="col text-center"><a href="#" onclick="modalTipoReporte(\''.$row["Correlativo"].'\');" class="nav-link">
                                <i class="far fa-file-pdf fa-2x" style="color:red"></i>
                            </a></div>';
                $sub_array[] = str_pad($row["Correlativo"], 8, 0, STR_PAD_LEFT)
                                .'<br><span class="right badge badge-secondary mt-1">'.date(FORMAT_DATE,strtotime($row["fechae"])).'</span>';
                $sub_array[] = $row["Nomper"];
                $sub_array[] = $row["cantFact"];
                $sub_array[] = $row["Destino"] . " - " . $row["NomperChofer"];
                $sub_array[] = '<div class="col text-center"><a href="#" onclick="" class="nav-link">
                                <img src="../../public/build/images/bs.png" width="25" height="25" border="0" />
                            </a></div>';
                $sub_array[] = '<div class="col text-center">
                                    <button type="button" onClick="modalVerDetalleDespacho(\''.$row["Correlativo"].'\');" id="'.$row["Correlativo"].'" class="btn btn-info btn-sm ver_detalles">Detalle</button>'." ".'
                                    <button type="button" onClick="modalEditarDespachos(\''.$row["Correlativo"].'\');"    id="'.$row["Correlativo"].'" class="btn btn-info btn-sm update">Editar</button>'." ".'
                                    <button type="button" onClick="EliminarUnDespacho(\''.$row["Correlativo"].'\');"      id="'.$row["Correlativo"] .'" class="btn btn-danger btn-sm eliminar">Eliminar</button>
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
        }
        break;


    case "buscar_despacho_por_correlativo":

        //obtenemos el valor enviado por ajax
        $correlativo = $_POST['correlativo'];

        //buscamos en la bd la cabecera del despacho
        $cabecera_despacho = $relacion->get_despacho_por_correlativo($correlativo);

        //validamos que la consulta de la cabecera tenga registro
        if(is_array($cabecera_despacho) == true and count($cabecera_despacho) > 0) {
            //si tiene se asignan a variables de salida
            $output["correl"] = str_pad($correlativo, 8, 0, STR_PAD_LEFT);
            $output["Destino"] = $cabecera_despacho[0]["Destino"]." - ".$cabecera_despacho[0]["NomperChofer"];
            $output["fechad"] = date(FORMAT_DATE, strtotime($cabecera_despacho[0]['fechad']));
            $output["vehiculo"] = $cabecera_despacho[0]['Placa']." ".$cabecera_despacho[0]['Modelo']." ".$cabecera_despacho[0]['Capacidad']." Kg";
            $output["cantFacturas"] = $cabecera_despacho[0]['cantFacturas'];

        }

        echo json_encode($output);
        break;

    case "buscar_destalle_despacho_por_correlativo":

        //obtenemos el valor enviado por ajax
        $correlativo = $_POST['correlativo'];

        //buscamos en la bd la el detalle
        $detalle_despacho = $relacion->get_detalle_despacho_por_correlativo($correlativo);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        //validamos que la consulta del detalle de despacho tenga registros
        if(is_array($detalle_despacho) == true and count($detalle_despacho) > 0) {

            foreach ($detalle_despacho as $row) {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $sub_array[] = $row["Numerod"];
                $sub_array[] = $row["codclie"];
                $sub_array[] = $row["descrip"];
                $sub_array[]  = date(FORMAT_DATETIME2,strtotime($row["fechae"]));
                $sub_array[]   = Strings::rdecimal($row["monto"], 2);
                $sub_array[] = '<div class="col text-center">
                                      <button type="button" onClick="modalMostrarDocumentoEnDespacho(\''.$row["Numerod"].'\',\''.$correlativo.'\');"   id="'.$row["Numerod"].'" class="btn btn-info btn-sm update">Editar</button>'." ".'
                                      <button type="button" onClick="modalEliminarDocumentoEnDespacho(\''.$row["Numerod"].'\',\''.$correlativo.'\');"  id="'.$row["Numerod"].'" class="btn btn-danger btn-sm eliminar">Eliminar</button>
                                </div>';

                $data[] = $sub_array;
            }
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data
        );

        echo json_encode($output);
        break;

    case "buscar_cabeceraDespacho_para_editar":

        //obtenemos el correlativo enviado por ajax
        $correlativo = $_POST['correlativo'];

        //consultamos el despacho y la lista de choferes y vehiculos
        $despacho = $relacion->get_despacho_por_correlativo($correlativo);
        $output["lista_choferes"] = Choferes::todos();
        $output["lista_vehiculos"] = Vehiculo::todos();

        if(is_array($despacho) == true and count($despacho) > 0) {
            //asignamos en una variable de salida los datos necesarios del despacho
            $output["destino"] = $despacho[0]["Destino"];
            $output["fecha"] = $despacho[0]['fechad'];
            $output["chofer"] = $despacho[0]["ID_Chofer"];
            $output["vehiculo"] = $despacho[0]['ID_Vehiculo'];
        }

        echo json_encode($output);

        break;

    case "actualizar_cabeceraDespacho_para_editar":

        $actualizar_despacho = $despachos->updateDespacho($_POST["correlativo"], $_POST["destino"], $_POST["chofer"], $_POST["vehiculo"], $_POST["fechad"]);

        ($actualizar_despacho) ? ($output["mensaje"] = "ACTUALIZADO CORRECTAMENTE") : ($output["mensaje"] = "ERROR") ;

        echo json_encode($output);

        break;

    case "actualizar_factura_en_despacho":

        $actualizar_documento = false;

        $correlativo = $_POST["correlativo"];
        $documento_nuevo = $_POST["documento_nuevo"];
        $documento_viejo = $_POST["documento_viejo"];

        //si no son iguales el documento ingresado al original
        if( !hash_equals($documento_nuevo, $documento_viejo))
        {
            //consultamos si la factura existe en la bd
            $existe_factura = $despachos->getFactura($documento_nuevo);

            //validamos si la factura existe
            if(count($existe_factura) > 0)
            {
                //consultamos si la factura existe en un despacho
                $existe_en_despacho = $despachos->getExisteFacturaEnDespachos($documento_nuevo);

                //validamos si el documento ingresado no exista en otro despacho
                if(count($existe_en_despacho) == 0)
                {
                    $factura_estado_1 = $relacion->get_factura_por_correlativo($correlativo);

                    if(count($factura_estado_1) == 0)
                    {
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
                    ($actualizar_documento) ? ($output["mensaje"] = "ACTUALIZADO CORRECTAMENTE") : ($output["mensaje"] = "ERROR AL ACTUALIZAR") ;

                } else {
                    ($output["mensaje"] = 'ATENCION! el numero de documento: '.$documento_nuevo.', ya fue despachado');
                }

            } else {
                ($output["mensaje"] = 'ATENCION! EL numero de documento: '.$documento_nuevo.', no existe en el sistema');
            }

        } else {
            ($output["mensaje"] = 'ATENCION! Por favor ingrese un documento diferente');
        }

        echo json_encode($output);

        break;

    case "eliminar_factura_en_despacho":

        $correlativo = $_POST["correlativo"];
        $nro_documento = $_POST["nro_documento"];

        $factura_estado_1 = $relacion->get_factura_por_correlativo($correlativo);

        if(count($factura_estado_1) != 0)
        {
            /**  enviar correo: despachos_elimina_fact **/
        }

        //eliminamos de un despacho en especifico
        $eliminar_documento = $despachos->deleteDetalleDespacho($correlativo, $nro_documento);

        //verificamos que se haya realizado la eliminacion del documento correctamente y devolvemos el mensaje
        ($eliminar_documento) ? $output["mensaje"] = 'ELIMINADO EXITOSAMENTE' : $output["mensaje"] = 'ERROR AL ELIMINAR';

        echo json_encode($output);

        break;


    case "agregar_factura_en_despacho":

        $insertar_documento = false;

        $correlativo = $_POST["correlativo"];
        $nro_documento = $_POST["documento_agregar"];

        //consultamos si la factura existe en la bd
        $existe_factura = $despachos->getFactura($nro_documento);

        //validamos si la factura existe
        if(count($existe_factura) > 0)
        {
            //consultamos si la factura existe en un despacho
            $existe_en_despacho = $despachos->getExisteFacturaEnDespachos($nro_documento);

            //validamos si el documento ingresado no exista en otro despacho
            if (count($existe_en_despacho) == 0)
            {
                $factura_estado_1 = $relacion->get_factura_por_correlativo($correlativo);

                if(count($factura_estado_1) == 0)
                {
                    //si cumple con todas las condiciones INSERTA la factura en un despacho en especifico
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
                    : ($output["mensaje"] = "ERROR AL INSERTAR") ;

            } else {
                ($output["mensaje"] = 'ATENCION! el numero de documento: '.$documento_nuevo.', ya fue despachado');
            }

        } else {
            ($output["mensaje"] = 'ATENCION! EL numero de documento: '.$documento_nuevo.', no existe en el sistema');
        }

        echo json_encode($output);

        break;


    case "eliminar_un_despacho":

        $correlativo = $_POST["correlativo"];

        $factura_estado_1 = $relacion->get_factura_por_correlativo($correlativo);

        if(count($factura_estado_1) != 0)
        {
            /**  enviar correo: despachos_elimina **/
        }

        //eliminamos de un despacho en especifico
        $eliminar_despacho = $despachos->deleteDespacho($correlativo);

        //verificamos que se haya realizado la eliminacion del documento correctamente y devolvemos el mensaje
        if($eliminar_despacho) {
            $output["mensaje"] = 'ELIMINADO EXITOSAMENTE';
            $output["icono"] = "success";
        } else {
            $output["mensaje"] = 'ERROR AL ELIMINAR';
            $output["icono"] = "error";
        }

        echo json_encode($output);

        break;


    case "listar_productos_de_un_despacho":

        //correlativo
        $correlativo = $_POST["correlativo"];

        //obtenemos los registros de los productos en dichos documentos
        $datos = $despachos->getProductosDespachoCreado($correlativo);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        $total_bultos = 0;
        $total_paq = 0;
        foreach ($datos as $row) {

            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            //REALIZAMOS PROCESOS DE CALCULO
            $bultos = 0;
            $paq = 0;
            if ($row["BULTOS"] > 0){
                $bultos = $row["BULTOS"];
            }
            if ($row["PAQUETES"] > 0){
                $paq = $row["PAQUETES"];
            }

            if ($row["EsEmpaque"] != 0){
                if ($row["PAQUETES"] > $row["CantEmpaq"]){

                    if ($row["CantEmpaq"] != 0) {
                        $bultos_total = $row["PAQUETES"] / $row["CantEmpaq"];
                    }else{
                        $bultos_total = 0;
                    }
                    $decimales = explode(".",$bultos_total);
                    $bultos_deci = $bultos_total - $decimales[0];
                    $paq = $bultos_deci * $row["CantEmpaq"];
                    $bultos = $decimales[0] + $bultos;
                }
            }
            $total_bultos += $bultos;
            $total_paq += $paq;

            //agregamos al sub array
            $sub_array[] = $row["CodItem"];
            $sub_array[] = $row["Descrip"];
            $sub_array[] = round($bultos);
            $sub_array[] = round($paq);

            //agregamos un registro al array principal
            $data[] = $sub_array;
        }

        //totalizado que se imprimira en la parte inferior de la tabla
        $output["total_bultos"] = $total_bultos;
        $output["total_paq"] = $total_paq;

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output['tabla'] = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);

        echo json_encode($output);


        break;

}