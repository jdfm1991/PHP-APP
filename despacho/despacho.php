<?php
require_once("../acceso/conexion.php");
require_once("../acceso/const.php");

require_once("../choferes/choferes_modelo.php");
$choferes = new Choferes();
$lista_choferes = $choferes->get_choferes();
require_once("../vehiculos/vehiculos_modelo.php");
$vehiculo = new Vehiculos();
$lista_vehiculos = $vehiculo->get_vehiculos();
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
                        <!-- Boton para modal crear nuevo despacho -->
                        <button class="btn btn-primary" id="btn_add_despacho" onclick="limpiar()" data-toggle="modal" data-target="#despachosModal">Nuevo Despacho  <i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                        <!-- Boton para modal buscar detalle de factura -->
                        <button class="btn btn-primary" id="add_facturas" onclick="limpiar()" data-toggle="modal" data-target="#buscarfactura">Ver Factura  <i class="fa fa-search-plus" aria-hidden="true"></i></button>
                        <!-- Boton para relacion de facturas por despachar -->
                        <button class="btn btn-primary" id="btn_x_despachar" onclick="limpiar()" data-toggle="modal" data-target="#sindespachar">Por Despachar  <i class="fa fa-truck" aria-hidden="true"></i></button>
                        <hr>
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Relación de Despachos</h3><!-- overflow:scroll; -->
                            </div>
                            <div class="card-body" style="width:auto;">
                                <table class="table table-hover table-condensed table-bordered table-striped" style="width:100%;" id="despacho_data">
                                    <thead style="background-color: #17A2B8;color: white;">
                                        <tr>
                                            <td class="text-center" data-toggle="tooltip" data-placement="top" title="Despacho">Despacho</td>
                                            <td class="text-center" data-toggle="tooltip" data-placement="top" title="Fecha">Fecha</td>
                                            <td class="text-center" data-toggle="tooltip" data-placement="top" title="Usuario">Usuario</td>
                                            <td class="text-center" data-toggle="tooltip" data-placement="top" title="Cant Fact">Cant Fact</td>
                                            <td class="text-center" data-toggle="tooltip" data-placement="top" title="Destino">Destino</td>
                                            <td class="text-center" data-toggle="tooltip" data-placement="top" title="Edit">Edit</td>
                                            <td class="text-center" data-toggle="tooltip" data-placement="top" title="Borra">Borra</td>
                                            <td class="text-center" data-toggle="tooltip" data-placement="top" title="Ver">Ver</td>
                                            <td class="text-center" data-toggle="tooltip" data-placement="top" title="Cobros">Cobros</td>
                                            <td class="text-center" data-toggle="tooltip" data-placement="top" title="Pdf">Pdf</td>
                                            <td class="text-center" data-toggle="tooltip" data-placement="top" title="Det">Det</td>
                                        </tr>
                                    </thead>
                                    <tfoot style="background-color: #ccc;color: white;">
                                        <tr>
                                            <td class="text-center">Despacho</td>
                                            <td class="text-center">Fecha</td>
                                            <td class="text-center">Usuario</td>
                                            <td class="text-center">Cant Fact</td>
                                            <td class="text-center">Destino</td>
                                            <td class="text-center">Edit</td>
                                            <td class="text-center">Borra</td>
                                            <td class="text-center">Ver</td>
                                            <td class="text-center">Cobros</td>
                                            <td class="text-center">Pdf</td>
                                            <td class="text-center">Det</td>
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
        <div class="modal fade"  id="despachosModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Agregar Despacho</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="despacho_form">
                            <label>Fecha del Despacho</label>
                            <input type="date" class="form-control"  id="fecha" name="fecha" >
                            <label>Chofer</label>
                            <select class="form-control custom-select" id="chofer" name="chofer" style="width: 100%;" required>
                                <option value="">Seleccione</option>
                                <?php
                                foreach ($lista_choferes as $query) {
                                    echo '<option value="' . $query['id'] . '">' . $query['Nomper'] . '</option>';} ?>
                                </select>
                                <label>Vehiculo</label>
                                <select class="form-control custom-select" id="vehiculo" name="vehiculo" style="width: 100%;" required>
                                    <option value="">Seleccione</option>
                                    <?php
                                    foreach ($lista_vehiculos as $query) {
                                        echo '<option value="' . $query['ID'] . '">' . $query['Modelo'] . "&nbsp;&nbsp;" . $query['Capacidad'] . " Kg" . '</option>';} ?>
                                    </select>
                                    <label>Destino</label>
                                    <input type="text" class="form-control input-sm" maxlength="120" id="destino" name="destino" >
                                    <br />
                                    <div class="modal-footer">
                                        <input type="hidden" name="id_despacho" id="id_despacho"/>
                                        <button type="submit" name="action" id="btnGuardar" class="btn btn-success pull-left" value="Add">Continuar</button>
                                        <button type="button" onclick="limpiar()" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Cerrar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content-wrapper -->
            <?php require_once("../footer.php");?>
            <script type="text/javascript" src="despacho.js"></script>
        </body>
        </html>
