<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("despachosrelacion_modelo.php");

//INSTANCIAMOS EL MODELO
$relacion = new DespachosRelacion();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_facturaEnDespachos":

        $numero = $_POST['nrfactb'];

        $datos = $relacion->getFacturaEnDespachos($_POST["nrfactb"]);

        $output["mensaje"] = '<div class="col text-center">';
        if(count($datos) > 0) {

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
            $sub_array[] = '<div class="col text-center"><a href="<?php echo SERVERURL; ?>despachosrelacion/despachosrelacion.php" class="nav-link">
                                <i class="far fa-edit fa-2x" style="color:green"></i>
                            </a></div>';
            $sub_array[] = '<div class="col text-center"><a href="<?php echo SERVERURL; ?>despachosrelacion/despachosrelacion.php" class="nav-link">
                                <i class="fas fa-minus-circle fa-2x" style="color:darkred"></i>
                            </a></div>';
            $sub_array[] = '<div class="col text-center"><a href="<?php echo SERVERURL; ?>despachosrelacion/despachosrelacion.php" class="nav-link">
                                <i class="fas fa-map-pin fa-2x" style="color:cornflowerblue"></i>
                            </a></div>';
            $sub_array[] = '<div class="col text-center"><a href="<?php echo SERVERURL; ?>despachosrelacion/despachosrelacion.php" class="nav-link">
                                <img src="../public/build/images/bs.png" width="25" height="25" border="0" />
                            </a></div>';
            $sub_array[] = '<div class="col text-center"><a href="<?php echo SERVERURL; ?>despachosrelacion/despachosrelacion.php" class="nav-link">
                                <i class="far fa-file-pdf fa-2x" style="color:red"></i>
                            </a></div>';
            $sub_array[] = '<div class="col text-center"><a href="<?php echo SERVERURL; ?>despachosrelacion/despachosrelacion.php" class="nav-link">
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