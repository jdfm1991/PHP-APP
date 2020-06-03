<?php
require_once("../acceso/conexion.php");
require_once("../acceso/const.php");
require_once("despachosrelacion_modelo.php");
$relacion = new DespachosRelacion();
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
                                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Edit">Edit</th>
                                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Borra">Borra</th>
                                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Ver">Ver</th>
                                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Cobros">Cobros</th>
                                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Pdf">Pdf</th>
                                    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Det">Det</th>
                                </tr>
                                </thead>
                                <tfoot style="background-color: #ccc;color: white;">
                                <tr>
                                    <th class="text-center">Despacho</th>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Usuario</th>
                                    <th class="text-center">Cant Fact</th>
                                    <th class="text-center">Destino</th>
                                    <th class="text-center">Edit</th>
                                    <th class="text-center">Borra</th>
                                    <th class="text-center">Ver</th>
                                    <th class="text-center">Cobros</th>
                                    <th class="text-center">Pdf</th>
                                    <th class="text-center">Det</th>
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
    <!-- Modal crear -->
    <div class="modal fade"  id="buscarxfacturaModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buscar por Factura</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label>Nro de documento</label>
                    <input type="text" class="form-control input-sm" maxlength="20" id="nrodocumento" name="nrodocumento" placeholder="Ingrese numero de documento" required >
                    <br />
                    <div id="detalle_despacho"></div>
                    <div class="modal-footer">
                        <button type="button" onclick="limpiar_campo_factura()" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Cerrar</button>
                        <button type="button" name="action" id="btnBuscarFactModal" class="btn btn-success pull-right" value="Add">Buscar</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- /.content-wrapper -->
<?php require_once("../footer.php");?>
<script type="text/javascript" src="despachosrelacion.js"></script>
</body>
</html>
