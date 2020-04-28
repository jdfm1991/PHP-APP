<?php
require_once("../acceso/conexion.php");
require_once("listadeprecio_modelo.php");
$precios = new Listadeprecio();

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

?>
<div class="card-header">
    <h3 class="card-title">Clientes</h3>
</div>
<div class="card-body" style="width:auto;">
    <table id="tablaprecios" class="table table-hover table-condensed table-bordered table-striped" style="width:100%;">
        <thead style="background-color: #17A2B8;color: white;">
            <tr>
                <th class="text-center" data-toggle="tooltip" data-placement="top" title="Código">Código</th>
                <th class="text-center" data-toggle="tooltip" data-placement="top" title="Producto">Producto</th>
                <th class="text-center" data-toggle="tooltip" data-placement="top" title="Marca">Marca</th>
                <!--BULTOS-->
                <th class="text-center" data-toggle="tooltip" data-placement="top" title="Bultos">Bultos</th>
                <?php switch ($sumap) {
                    case 1: ?>
                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Precio<?php echo $sumap2; ?>">Precio <?php echo $sumap2; ?> Bulto</th>
                    <?php break;
                    case 2: ?>
                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Precio<?php if ($p1 == 1){ echo $p1; }else{ echo $p2;} ?>">Precio <?php if ($p1 == 1){ echo $p1; }else{ echo $p2;} ?> Bulto</th>
                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Precio<?php if ($p3 == 3){ echo $p3; }else{ echo $p2;} ?>">Precio <?php if ($p1 == 3){ echo $p3; }else{ echo $p2;} ?> Bulto</th>
                    <?php break;
                    default: /** 0 || 3**/?>
                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Precio 1">Precio 1 Bulto</th>
                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Precio 2">Precio 2 Bulto</th>
                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Precio 3">Precio 3 Bulto</th>
                <?php } ?>
                <!--PAQUETES-->
                <th class="text-center" data-toggle="tooltip" data-placement="top" title="Código">Paquete</th>
                <?php switch ($sumap) {
                    case 1: ?>
                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Precio<?php echo $sumap2; ?>">Precio <?php echo $sumap2; ?> Paquete</th>
                    <?php break;
                    case 2: ?>
                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Precio<?php if ($p1 == 1){ echo $p1; }else{ echo $p2;} ?>">Precio <?php if ($p1 == 1){ echo $p1; }else{ echo $p2;} ?> Paquete</th>
                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Precio<?php if ($p3 == 3){ echo $p3; }else{ echo $p2;} ?>">Precio <?php if ($p3 == 3){ echo $p3; }else{ echo $p2;} ?> Paquete</th>
                    <?php break;
                    default: /** 0 || 3**/?>
                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Precio 1">Precio 1 Paquete</th>
                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Precio 2">Precio 2 Paquete</th>
                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Precio 3">Precio 3 Paquete</th>
                <?php }
                if ($cubi == 1) { ?>
                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Cubicaje">Cubicaje</th>
                <?php } ?>
            </tr>
        </thead>
        <tfoot style="background-color: #ccc;color: white;">
            <tr>
                <th class="text-center">Código</th>
                <th class="text-center">Producto</th>
                <th class="text-center">Marca</th>
                <!--BULTOS-->
                <th class="text-center">Bultos</th>
                <?php switch ($sumap) {
                    case 1: ?>
                    <th class="text-center">Precio <?php echo $sumap2; ?> Bulto</th>
                    <?php break;
                    case 2: ?>
                    <th class="text-center">Precio <?php if ($p1 == 1){ echo $p1; }else{ echo $p2;} ?> Bulto</th>
                    <th class="text-center">Precio <?php if ($p3 == 3){ echo $p3; }else{ echo $p2;} ?> Bulto</th>
                    <?php break;
                    default: /** 0 || 3**/?>
                    <th class="text-center">Precio 1 Bulto</th>
                    <th class="text-center">Precio 2 Bulto</th>
                    <th class="text-center">Precio 3 Bulto</th>
                <?php } ?>
                <!--PAQUETES-->
                <th class="text-center">Paquete</th>
                <?php switch ($sumap) {
                    case 1: ?>
                    <th class="text-center">Precio <?php echo $sumap2; ?> Paquete</th>
                    <?php break;
                    case 2: ?>
                    <th class="text-center">Precio <?php if ($p1 == 1){ echo $p1; }else{ echo $p2;} ?> Paquete</th>
                    <th class="text-center">Precio <?php if ($p3 == 3){ echo $p3; }else{ echo $p2;} ?> Paquete</th>
                    <?php break;
                    default: /** 0 || 3**/?>
                    <th class="text-center">Precio 1 Paquete</th>
                    <th class="text-center">Precio 2 Paquete</th>
                    <th class="text-center">Precio 3 Paquete</th>
                <?php }
                if ($cubi == 1) { ?>
                    <th class="text-center">Cubicaje</th>
                <?php } ?>
            </tr>
            </tfoot> <?php

            $lprecios = $precios->getListadeprecios($marcas, $depos, $exis, $orden);
            $num = count($lprecios);

            if($num > 0) {?>
                <tbody>
                    <?php
                    foreach ($lprecios as $i) {

                        if ($i['esexento']) {
                            $precio1 = $i['precio1'] * $iva;
                            $precio2 = $i['precio2'] * $iva;
                            $precio3 = $i['precio3'] * $iva;
                            $preciou1 = $i['preciou1'] * $iva;
                            $preciou2 = $i['preciou2'] * $iva;
                            $preciou3 = $i['preciou3'] * $iva;
                        } else {
                            $precio1 = $i['precio1'];
                            $precio2 = $i['precio2'];
                            $precio3 = $i['precio3'];
                            $preciou1 = $i['preciou1'];
                            $preciou2 = $i['preciou2'];
                            $preciou3 = $i['preciou3'];
                        } ?>
                        <tr>
                            <td><?php echo $i['codprod'] ?></td>
                            <td><?php echo $i['descrip'] ?></td>
                            <td><?php echo $i['marca'] ?></td>
                            <!--BULTOS-->
                            <td><?php echo round($i['existen']) ?></td>
                            <?php switch ($sumap) {
                                case 1: ?>
                                <td><?php if ($i['esexento'] == 0) { echo number_format($i['precio'. $sumap2 ]* $iva, 2, ",", "."); } else { echo number_format($i['precio'. $sumap2 ], 2, ",", "."); } ?></td>
                                <?php break;
                                case 2: ?>
                                <td><?php if ($p1 == 1) { echo number_format($precio1, 2, ",", "."); } else { echo number_format($precio2, 2, ",", "."); } ?></td>
                                <td><?php if ($p3 == 3) { echo number_format($precio3, 2, ",", "."); } else { echo number_format($precio2, 2, ",", "."); } ?></td>
                                <?php break;
                                default: /** 0 || 3**/?>
                                <td><?php echo number_format($precio1, 2, ",", ".") ?></td>
                                <td><?php echo number_format($precio2, 2, ",", ".") ?></td>
                                <td><?php echo number_format($precio3, 2, ",", ".") ?></td>
                            <?php } ?>
                            <!--PAQUETES-->
                            <td><?php echo round($i['exunidad']) ?></td>
                            <?php switch ($sumap) {
                                case 1: ?>
                                <td><?php if ($i['esexento'] == 0) { echo number_format($i['preciou'. $sumap2 ]* $iva, 2, ",", "."); } else { echo number_format($i['preciou'. $sumap2 ], 2, ",", "."); } ?></td>
                                <?php break;
                                case 2: ?>
                                <td><?php if ($p1 == 1) { echo number_format($preciou1, 2, ",", "."); } else { echo number_format($preciou2, 2, ",", "."); } ?></td>
                                <td><?php if ($p3 == 3) { echo number_format($preciou3, 2, ",", "."); } else { echo number_format($preciou2, 2, ",", "."); } ?></td>
                                <?php break;
                                default: /** 0 || 3**/?>
                                <td><?php echo number_format($preciou1, 2, ",", ".") ?></td>
                                <td><?php echo number_format($preciou2, 2, ",", ".") ?></td>
                                <td><?php echo number_format($preciou3, 2, ",", ".") ?></td>
                            <?php }
                            if ($cubi == 1) {?>
                                <td><?php echo $i['cubicaje'] ?></td>
                            <?php } ?>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody><?php
                }?>
            </table>
            <div align="center">
                <br>
                <br><p>Total de Productos:<code><?php echo "  $num  "; ?></code></p><br>
                <button type="button" class="btn btn-info" id="btn_excel">Exportar a Excel</button>
                <button type="button" class="btn btn-info" id="btn_pdf">Exportar a PDF</button>
                <br>
                <br>
            </div>
        </div>


