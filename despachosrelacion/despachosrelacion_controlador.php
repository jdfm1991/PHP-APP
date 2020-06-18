<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("despachosrelacion_modelo.php");
require_once("../despachos/despachos_modelo.php");
require_once("../choferes/choferes_modelo.php");
require_once("../vehiculos/vehiculos_modelo.php");

//INSTANCIAMOS EL MODELO
$relacion = new DespachosRelacion();
$despachos = new Despachos();
$choferes = new Choferes();
$vehiculos = new Vehiculos();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_facturaEnDespachos":

        $numero = $_POST['nrfactb'];

        $datos = $despachos->getFacturaEnDespachos($_POST["nrfactb"]);

        $output["mensaje"] = '<div class="col text-center">';
        if(is_array($datos) == true AND count($datos) > 0) {

            $output["mensaje"] .= "<strong>Nro de Documento: </strong>".$_POST['nrfactb'].", <strong>Despacho Nro: </strong> ".str_pad($datos[0]['Correlativo'], 8, 0, STR_PAD_LEFT).",</br> ";
            $output["mensaje"] .= "<strong>Fecha Emision: </strong>".date("d/m/Y h:i A", strtotime($datos[0]['fechae'])).",<strong> Destino: </strong>".$datos[0]["Destino"]." - ".$datos[0]["NomperChofer"]."</br>";

            if (isset($datos[0]['fecha_liqui']) AND isset($datos[0]['monto_cancelado'])){

                $output["mensaje"] .= "</br><strong>PAGO:</strong> ".date("d/m/Y", strtotime($datos[0]['fecha_liqui'])).", <strong>POR UN MONTO DE:</strong> ".number_format($datos[0]['monto_cancelado'], 1, ",", ".")." BsS";
            }else{
                $output["mensaje"] .= "</br>DOCUMENTO NO LIQUIDADO";
            }
        } else {
            $output["mensaje"] .= "EL DOCUMENTO INGRESADO <strong>NO A SIDO DESPACHADO</strong>";
        }
        $output["mensaje"] .= '</div>';

        echo json_encode($output);

        break;


    case "buscar_cabeceraDespacho":

        $correlativo = $_POST['correlativo'];

        $datos = $relacion->get_despacho_por_correlativo($correlativo);

        $output["mensaje"] = '<div class="col ml-2">';
        if(count($datos) > 0) {

            $output["mensaje"] .= "<strong>Despacho nro: </strong>".str_pad($correlativo, 8, 0, STR_PAD_LEFT)."</br>";
            $output["mensaje"] .= "<strong>Destino: </strong>".$datos[0]["Destino"]." - ".$datos[0]["NomperChofer"]."</br>";
            $output["mensaje"] .= "<strong>Fecha Despacho: </strong>".date("d/m/Y", strtotime($datos[0]['fechad']))."</br></br>";
            $output["mensaje"] .= "<strong>Vehiculo: </strong>{$datos[0]['Placa']} {$datos[0]['Modelo']} {$datos[0]['Capacidad']} Kg".'&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" onClick="modalMostrarEditarDespacho(\''.$correlativo.'\');" class="btn btn-outline-primary btn-xs">Editar</button>'."</br></br>";
            $output["mensaje"] .= "<strong>Facturas: </strong>{$datos[0]['cantFacturas']}";

        }
        $output["mensaje"] .= '</div>';

        echo json_encode($output);

        break;

    case "buscar_cabeceraDespacho_para_editar":

        $correlativo = $_POST['correlativo'];

        $despacho = $relacion->get_despacho_por_correlativo($correlativo);
        $lista_choferes = $choferes->get_choferes();
        $lista_vehiculos = $vehiculos->get_vehiculos();

        $output["destino"] = $despacho[0]["Destino"];
        $output["fecha"] = $despacho[0]['fechad'];

        $output["chofer"] = '<option name="" value="">Seleccione</option>';
        if(count($lista_choferes) > 0) {
            foreach ($lista_choferes as $chofer)
            {
               if($despacho[0]["ID_Chofer"] == $chofer['Cedula']) {
                   $output["chofer"] .= '<option value="' . $chofer['Cedula'] . '" selected>' . $chofer['Nomper'] . '</option>';
               } else {
                   $output["chofer"] .= '<option value="' . $chofer['Cedula'] . '">' . $chofer['Nomper'] . '</option>';
               }
            }

        }

        $output["vehiculo"] = '<option name="" value="">Seleccione</option>';
        if(count($lista_vehiculos) > 0) {
            foreach ($lista_vehiculos as $vehiculo)
            {
                if($despacho[0]["ID_Vehiculo"] == $vehiculo['ID']) {
                    $output["vehiculo"] .= '<option value="' . $vehiculo['ID'] . '" selected>' . $vehiculo['Modelo'] . "&nbsp;&nbsp;" . $vehiculo['Capacidad'] . " Kg" . '</option>';
                } else {
                    $output["vehiculo"] .= '<option value="' . $vehiculo['ID'] . '">' . $vehiculo['Modelo'] . "&nbsp;&nbsp;" . $vehiculo['Capacidad'] . " Kg" . '</option>';
                }
            }
        }

        echo json_encode($output);

        break;

    case "actualizar_cabeceraDespacho_para_editar":

        $actualizar_despacho = $despachos->updateDespacho($_POST["correlativo"], $_POST["destino"], $_POST["chofer"], $_POST["vehiculo"], $_POST["fechad"]);

        ($actualizar_despacho) ? ($output["mensaje"] = "ACTUALIZADO CORRECTAMENTE") : ($output["mensaje"] = "ERROR") ;

        echo json_encode($output);

        break;

    case "actualizar_factura_en_despacho":

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

                    if(count($factura_estado_1) != 0)
                    {
                        /**  enviar correo: despachos_edita_3 **/
                    }

                    //si cumple con todas las condiciones ACTUALIZA la factura en un despacho en especifico
                    $actualizar_documento = $despachos->updateDetalleDespacho($correlativo, $documento_nuevo, $documento_viejo);

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
            if(count($existe_en_despacho) == 0)
            {
                $factura_estado_1 = $relacion->get_factura_por_correlativo($correlativo);

                if(count($factura_estado_1) != 0)
                {
                    /**  enviar correo: despachos_edita_4 **/
                }

                //si cumple con todas las condiciones INSERTA la factura en un despacho en especifico
                $insertar_documento = $despachos->insertarDetalleDespacho($correlativo, $nro_documento, 'A');

                //verificamos que se haya realizado la insercion correctamente y devolvemos el mensaje
                ($insertar_documento) ? ($output["mensaje"] = "INSERTADO CORRECTAMENTE") : ($output["mensaje"] = "ERROR AL INSERTAR") ;

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


    case "listar_despacho_por_correlativo":

        $correlativo = $_POST['correlativo'];

        $datos = $relacion->get_detalle_despacho_por_correlativo($correlativo);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            $sub_array[] = $row["Numerod"];
            $sub_array[] = $row["codclie"];
            $sub_array[] = $row["descrip"];
            $sub_array[] = date("d/m/Y h:i A",strtotime($row["fechae"]));
            $sub_array[] = number_format($row["monto"], 2, ",", ".");
            $sub_array[] = '<div class="col text-center"></button>'." ".'<button type="button" onClick="modalMostrarDocumentoEnDespacho(\''.$row["Numerod"].'\',\''.$correlativo.'\');"  id="'.$row["Numerod"].'" class="btn btn-info btn-sm update">Editar</button>'." ".'<button type="button" onClick="modalEliminarDocumentoEnDespacho(\''.$row["Numerod"].'\',\''.$correlativo.'\');"  id="'.$row["Numerod"].'" class="btn btn-danger btn-sm eliminar">Eliminar</button></div>';


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

        $_SESSION["total_bultos"] = $total_bultos;
        $_SESSION["total_paq"] = $total_paq;

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $results = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);
        echo json_encode($results);

        break;

    case "listar_totales_paq_bul_despacho":

        if( isset($_SESSION["total_bultos"]) && isset($_SESSION["total_paq"]) ) {
            $output["total_bultos"] = $_SESSION["total_bultos"];
            $output["total_paq"] = $_SESSION["total_paq"];

            unset($_SESSION['total_bultos']);
            unset($_SESSION['total_paq']);
        }

        echo json_encode($output);

        break;


    case "listar_RelacionDespachos":

        $datos = $relacion->getRelacionDespachos();

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();


        foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            $sub_array[] = str_pad($row["Correlativo"], 8, 0, STR_PAD_LEFT);
            $sub_array[] = date("d/m/Y",strtotime($row["fechae"]));
            $sub_array[] = $row["Nomper"];
            $sub_array[] = $row["cantFact"];
            $sub_array[] = $row["Destino"] . " - " . $row["NomperChofer"];
            $sub_array[] = '<div class="col text-center"><a href="#" onclick="modalEditarDespachos(\''.$row["Correlativo"].'\');" class="nav-link">
                                <i class="far fa-edit fa-2x" style="color:green"></i>
                            </a></div>';
            $sub_array[] = '<div class="col text-center"><a href="#" onclick="EliminarUnDespacho(\''.$row["Correlativo"].'\');" class="nav-link">
                                <i class="fas fa-minus-circle fa-2x" style="color:darkred"></i>
                            </a></div>';
            $sub_array[] = '<div class="col text-center"><a href="#" onclick="modalVerDetalleDespacho(\''.$row["Correlativo"].'\');" class="nav-link">
                                <i class="fas fa-search fa-2x" style="color:cornflowerblue"></i>
                            </a></div>';
            $sub_array[] = '<div class="col text-center"><a href="#" onclick="" class="nav-link">
                                <img src="../public/build/images/bs.png" width="25" height="25" border="0" />
                            </a></div>';
            $sub_array[] = '<div class="col text-center"><a href="#" onclick="abrirReporteProductosDeUnDepacho(\''.$row["Correlativo"].'\');" class="nav-link">
                                <i class="far fa-file-pdf fa-2x" style="color:red"></i>
                            </a></div>';
            $sub_array[] = '<div class="col text-center"><a href="#" onclick="abrirReporteDetalleCompletoDeUnDepacho(\''.$row["Correlativo"].'\');" class="nav-link">
                                <i class="fas fa-info-circle fa-2x" style="color:darkgrey"></i>
                            </a></div>';

	  /**</div></td>
	  <td width="55"><div align="center"><div align="center"><a href="index.php?&page=despachos_edita&mod=1&correl=<?php echo mssql_result($consul_planillas,$i,"correl"); ?>"> <img src="images/edt.png" width="19" height="18" border="0" /></a></div></div></td>
	  <td width="51"><div align="center"><a href="#" onclick="elimna_des('<?php echo str_pad(mssql_result($consul_planillas,$i,"correl"), 8, 0, STR_PAD_LEFT); ?>','<?php echo mssql_result($consul_planillas,$i,"correl"); ?>')"> <img src="images/cancel.png" width="15" height="15" border="0" /></a></div></td>
	  <td width="43"><div align="center"><a href="index.php?&page=pdf_despacho&mod=1&correl=<?php echo mssql_result($consul_planillas,$i,"correl"); ?>"> <img src="images/search.png" width="19" height="18" border="0" /></a></div></td>
	  <td width="43"><div align="center"><a href="index.php?&page=despachos_cobros&mod=1&correl=<?php echo mssql_result($consul_planillas,$i,"correl"); ?>"> <img src="images/bs.png" width="19" height="18" border="0" /></a></div></td>
      <td width="56"><div align="center"><a href="pdf_despacho2.php?&correl=<?php echo mssql_result($consul_planillas,$i,"correl"); ?>" target="_blank"><img border="0" src="images/imp.gif" width="19" height="18" /></a></div></td>
      <td width="46"><div align="center"><a href="pdf_despacho3.php?&correl=<?php echo mssql_result($consul_planillas,$i,"correl"); ?>" target="_blank"><img border="0" src="images/indicadores.png" width="19" height="18" /></a></div></td>
**/
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