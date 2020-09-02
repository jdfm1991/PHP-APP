<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("inventarioglobal_modelo.php");
require_once("../costodeinventario/costodeinventario_modelo.php");

//INSTANCIAMOS EL MODELO
$invglobal = new InventarioGlobal();
$costo = new CostodeInventario();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_inventarioglobal":

        if (isset($_POST['depo'])) {
            $numero = $_POST['depo'];
        } else {
            $numero = array();
        }
        $edv = "";
        if (count($numero) > 0) {
            foreach ($numero as $i) {
                $edv .= "'" . $i . "',";
            }
        }

        $ffin = date('Y-m-d');
        $dato = explode("-", $ffin); //Hasta
        $aniod = $dato[0]; //año
        $mesd = $dato[1]; //mes
        $diad = "01"; //dia
        $fini = $aniod . "-01-01";
        $t = 0;


        break;
}



?>    
    <div class="card-header">
        <h3 class="card-title">Costos e Inventario</h3>
    </div>
    <div class="card-body table-responsive p-0" style="width:100%; height: 350px;">
        <table class="table table-hover table-condensed table-bordered table-striped table-head-fixed text-nowrap" id="inventarioglobal_data">
            <thead class="bg-light color-palette">
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Cantidad Bultos por Despachar</th>
                    <th>Cantidad Paquetes por Despachar</th>
                    <th>Cantidad Bultos Sistema</th>
                    <th>Cantidad Paquetes Sistema</th>
                    <th>Total Inventario Bultos</th>
                    <th>Total Inventario Paquetes</th>
                </tr>
            </thead>
            <tbody>
                <?php

                $devolucionesDeFactura = $invglobal->getDevolucionesDeFactura($almacen, $fini, $ffin);
                foreach ($devolucionesDeFactura as $dato) {
                    $coditem[$t] = $dato['coditem'];
                    $cantidad[$t] = $dato['cantidad'];
                    $tipo[$t] = $dato['esunid'];
                    $t += 1;
                }

            /*     $query = costo->getCostosdEinventario($edv, $marca); */

                foreach ($query as $i) {
                    if ($i['display'] == 0) {
                        $cdisplay = 0;
                    } else {
                        $cdisplay = $i['costo'] / $i['display'];
                    }
                ?>
                    <tr>
                   <td></td>
                   <td></td>
                   <td></td>
                   <td></td>
                   <td></td>
                   <td></td>
                   <td></td>

                    </tr>
                <?php
                    $costos += $i['costo'];
                    $costos_p += $cdisplay;
                    $precios += $i['precio'];
                    $bultos += $i['bultos'];
                    $paquetes += $i['paquetes'];
                    $tot_cos_bultos += ($i['costo'] * $i['bultos']);
                    $tot_cos_paquetes += ($cdisplay * $i['paquetes']);
                    $tot_tara += $i['tara'];
                } ?>
                <tr bgcolor="#17a2b8">
                    <td colspan="3" align="right">Totales: </td>
                    <td><?php echo number_format($costos, 2, ",", ".") ?></td>
                    <td><?php echo number_format($costos_p, 2, ",", ".") ?></td>
                    <td><?php echo number_format($precios, 2, ",", ".") ?></td>
                    <td><?php echo number_format($bultos, 2, ",", ".") ?></td>
                    <td><?php echo number_format($paquetes, 2, ",", ".") ?></td>
                    <td><?php echo number_format($tot_cos_bultos, 2, ",", ".") ?></td>
                    <td><?php echo number_format($tot_cos_paquetes, 2, ",", ".") ?></td>
                    <td><?php echo number_format($tot_tara, 2, ",", ".") ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <div align="center">
        <?php
        if (count($numero) > 0) {
            $depo = "";
            foreach ($numero as $i)
                $depo .= $i . "-";
        }
        ?>
        <button type="button" class="btn btn-info" onclick="window.open('costos_inv_excel.php?&marca=<?php echo $_POST['marca']; ?>&depo=<?php echo $depo; ?>', '_blank');">Exportar a Excel</button>
        <button type="button" class="btn btn-info" onclick="window.open('costos_inv_pdf.php?&marca=<?php echo $_POST['marca']; ?>&depo=<?php echo $depo; ?>', '_blank');">Exportar a PDF</button>
    </div>
    <br>