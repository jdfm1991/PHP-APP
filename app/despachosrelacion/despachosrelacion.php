<?php
require_once("../acceso/conexion.php");
require_once("../acceso/const.php");
?>
<!DOCTYPE html>
<html>
<!-- head -->
<?php require_once("../header.php");?>
<body class="hold-transition sidebar-mini layout-fixed">
<?php require_once("../menu_lateral.php");?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row">
            <div class="col-sm-12">
                <div class="card-body">
                    <button class="btn btn-primary" id="buscarxfact_button" onclick="limpiar_campo_factura()" data-toggle="modal" data-target="#buscarxfacturaModal"><i class="fa fa-search" aria-hidden="true"></i> Buscar por Factura</button>
                    <hr>
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Relación de Despachos</h3><!-- overflow:scroll; -->
                        </div>
                        <div class="card-body" style="width:auto;">
                            <table class="table table-hover table-condensed table-bordered table-striped" style="width:100%;" id="relacion_data">
                                <thead style="background-color: #17A2B8;color: white;">
                                <tr>
                                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Despacho">Despacho</th>
                                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Fecha">Fecha</th>
                                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Usuario">Usuario</th>
                                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Cant Fact">Cant Fact</th>
                                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Destino">Destino</th>
                                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Editar">Editar</th>
                                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Borrar">Borrar</th>
                                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Ver">Ver</th>
                                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Cobros">Cobros</th>
                                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Pdf">Pdf</th>
                                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Detalle">Detalle</th>
                                </tr>
                                </thead>
                                <tfoot style="background-color: #ccc;color: white;">
                                <tr>
                                    <th class="text-center">Despacho</th>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Usuario</th>
                                    <th class="text-center">Cant Fact</th>
                                    <th class="text-center">Destino</th>
                                    <th class="text-center">Editar</th>
                                    <th class="text-center">Borrar</th>
                                    <th class="text-center">Ver</th>
                                    <th class="text-center">Cobros</th>
                                    <th class="text-center">Pdf</th>
                                    <th class="text-center">Detalle</th>
                                </tr>
                                </tfoot>
                                <tbody>
                                <!-- TD de la tabla que se pasa por ajax -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal bucar factura -->
    <?php include '../despachos/modales/buscar_factura_modal.html' ?>

    <!-- Modal editar despachos -->
    <?php include 'modales/editar_despachos_modal.html' ?>

    <!-- Modal ver productos de un despcho -->
    <?php include 'modales/ver_productos_factura_modal.html' ?>

</div>
<!-- /.content-wrapper -->
<?php require_once("../footer.php");?>
<script type="text/javascript" src="despachosrelacion.js"></script>
</body>
</html>
