<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

if (!isset($_SESSION['cedula'])) {
    session_destroy(); Url::redirect(URL_APP);
}
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
                            <button class="btn btn-primary" id="add_button" onclick="mostrar()" data-toggle="modal" data-target="#vehiculoModal"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Vehiculos</button>
                            <hr>
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Vehiculos Registrados</h3><!-- overflow:scroll; -->
                                </div>
                                <div class="card-body" style="width:auto;">
                                    <table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="vehiculo_data">
                                        <thead style="background-color: #17A2B8;color: white;">
                                            <tr>
                                                <td class="text-center" title="# Placa"># Placa</td>
                                                <td class="text-center" title="Modelo">Modelo</td>
                                                <td class="text-center" title="Capacidad Kg">Capacidad Kg</td>
                                                <td class="text-center" title="Volumen Cm3">Volumen Cm3</td>
                                                <td class="text-center" title="Fecha de Registro">Fecha de Registro</td>
                                                <td class="text-center" title="Acción">Acciónes</td>
                                            </tr>
                                        </thead>
                                        <tfoot style="background-color: #ccc;color: white;">
                                            <tr>
                                                <td class="text-center"># Placa</td>
                                                <td class="text-center">Modelo</td>
                                                <td class="text-center">Capacidad Kg</td>
                                                <td class="text-center">Volumen Cm3</td>
                                                <td class="text-center">Fecha de Registro</td>
                                                <td class="text-center">Acciónes</td>
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
            <?php include 'modales/crear_o_editar_vehiculo.html' ?>
        </div>
        <!-- /.content-wrapper -->
        <?php require_once("../footer.php");?>
        <script type="text/javascript" src="vehiculos.js"></script><?php
    }
    ?>
</body>
</html>
