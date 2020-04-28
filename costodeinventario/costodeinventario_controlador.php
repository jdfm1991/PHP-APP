<?php
require_once("../acceso/conexion.php");
require_once("costodeinventario_modelo.php");
$costo = new CostodeInventario();


$marca = $_POST['marca'];

if(isset($_POST['depo'])){
    $numero = $_POST['depo'];
} else {
    $numero = array();
}
$edv = "";
if(count($numero)>0) {
    foreach ($numero AS $i) {
        $edv .= "'" . $i . "',";
    }
}

$costos = 0;
$costos_p = 0;
$precios = 0;
$bultos = 0;
$paquetes = 0;
$tot_cos_bultos = 0;
$tot_cos_paquetes = 0;
$tot_tara = 0;

?>

<div class="card-header">
    <h3 class="card-title">Costos de Inventario</h3>
</div>
<div id="relacion_costoseinventario" class="card-body table-responsive p-0" style="width:100%; height:50%;">
    <table class="table table-hover table-condensed table-bordered table-striped table-head-fixed text-nowrap" id="tablacostos">
        <thead class="bg-light color-palette">
            <tr>
                <th>Codprod</th>
                <th>Descripcion</th>
                <th>Marca</th>
                <th>Costo Bultos</th>
                <th>Costo Unid.</th>
                <th>Precio</th>
                <th>Bultos</th>
                <th>Paq.</th>
                <th>Total Costo Bultos</th>
                <th>Total Costo Unidades</th>
                <th>Tara</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $costos1 = $costo->getCostosdEinventario($edv, $marca);

            foreach ($costos1 as $i) {
                if ($i['display'] == 0) {
                    $cdisplay = 0;
                } else {
                    $cdisplay = $i['costo'] / $i['display'];
                }
                ?>
                <tr>
                    <td><?php echo $i['codprod'] ?></td>
                    <td><?php echo $i['descrip'] ?></td>
                    <td><?php echo $i['marca'] ?></td>
                    <td><?php echo number_format($i['costo'],2, ",", ".") ?></td>
                    <td><?php echo number_format($cdisplay,2, ",", ".") ?></td>
                    <td><?php echo number_format($i['precio'],2, ",", ".") ?></td>
                    <td><?php echo number_format($i['bultos'],2, ",", ".") ?></td>
                    <td><?php echo number_format($i['paquetes'],2, ",", ".") ?></td>
                    <td><?php echo number_format($i['costo'] * $i['bultos'],2, ",", ".") ?></td>
                    <td><?php echo number_format($cdisplay * $i['paquetes'],2, ",", ".") ?></td>
                    <td><?php echo number_format($i['tara'],2, ",", ".") ?></td>
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
            }?>
            <tr bgcolor="#17a2b8">
                <td colspan="3" align="right">Totales: </td>
                <td><?php echo number_format($costos,2, ",", ".") ?></td>
                <td><?php echo number_format($costos_p,2, ",", ".") ?></td>
                <td><?php echo number_format($precios,2, ",", ".") ?></td>
                <td><?php echo number_format($bultos,2, ",", ".") ?></td>
                <td><?php echo number_format($paquetes,2, ",", ".") ?></td>
                <td><?php echo number_format($tot_cos_bultos,2, ",", ".") ?></td>
                <td><?php echo number_format($tot_cos_paquetes,2, ",", ".") ?></td>
                <td><?php echo number_format($tot_tara,2, ",", ".") ?></td>
            </tr>
        </tbody>
    </table>
</div>
<br>
<div align="center">
    <?php
    if(count($numero)>0) {
        $depo = "";
        foreach ($numero AS $i)
            $depo .= $i . "-";
    }
    ?>    <br>
    <button type="button" class="btn btn-info" onclick="window.open('costos_inv_excel.php?&marca=<?php echo $_POST['marca'];?>&depo=<?php echo $depo; ?>', '_blank');" >Exportar a Excel</button>
    <button type="button" class="btn btn-info" onclick="window.open('costos_inv_pdf.php?&marca=<?php echo $_POST['marca'];?>&depo=<?php echo $depo; ?>', '_blank');" >Exportar a PDF</button><br><br>
</div>
