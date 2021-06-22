<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
require_once("../../config/conexion.php");

if (!isset($_SESSION['cedula'])) {
    session_destroy(); Url::redirect(URL_APP);
}
?>
?>
<!DOCTYPE html>
<html>
<!-- head -->
<?php require_once("../header.php");?>
<body class="hold-transition sidebar-mini layout-fixed">
    <?php require_once("../menu_lateral.php");
    if (!PermisosHelpers::verficarAcceso( Functions::getNameDirectory() )) {
        include ('../errorNoTienePermisos.php');
    }
    else { ?>
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
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('despacho')?>"><?=Strings::titleFromJson('despacho')?></th>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('fecha')?>"><?=Strings::titleFromJson('fecha')?></th>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('usuario')?>"><?=Strings::titleFromJson('usuario')?></th>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('cantidad_numerod')?>"><?=Strings::titleFromJson('cantidad_numerod')?></th>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('destino')?>"><?=Strings::titleFromJson('destino')?></th>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('editar')?>"><?=Strings::titleFromJson('editar')?></th>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('borrar')?>"><?=Strings::titleFromJson('borrar')?></th>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('ver')?>"><?=Strings::titleFromJson('ver')?></th>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('cobros')?>"><?=Strings::titleFromJson('cobros')?></th>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('pdf')?>"><?=Strings::titleFromJson('pdf')?></th>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('detalle')?>"><?=Strings::titleFromJson('detalle')?></th>
                                        </tr>
                                        </thead>
                                        <tfoot style="background-color: #ccc;color: white;">
                                        <tr>
                                            <th class="text-center"><?=Strings::titleFromJson('despacho')?></th>
                                            <th class="text-center"><?=Strings::titleFromJson('fecha')?></th>
                                            <th class="text-center"><?=Strings::titleFromJson('usuario')?></th>
                                            <th class="text-center"><?=Strings::titleFromJson('cantidad_numerod')?></th>
                                            <th class="text-center"><?=Strings::titleFromJson('destino')?></th>
                                            <th class="text-center"><?=Strings::titleFromJson('editar')?></th>
                                            <th class="text-center"><?=Strings::titleFromJson('borrar')?></th>
                                            <th class="text-center"><?=Strings::titleFromJson('ver')?></th>
                                            <th class="text-center"><?=Strings::titleFromJson('cobros')?></th>
                                            <th class="text-center"><?=Strings::titleFromJson('pdf')?></th>
                                            <th class="text-center"><?=Strings::titleFromJson('detalle')?></th>
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
        <script type="text/javascript" src="<?php echo URL_HELPERS_JS ?>Number.js"></script>
        <script type="text/javascript" src="despachosrelacion.js"></script><?php
    }
    ?>
</body>
</html>
