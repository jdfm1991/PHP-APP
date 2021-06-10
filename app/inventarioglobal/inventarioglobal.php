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
<?php require_once("../header.php"); ?>

<body class="hold-transition sidebar-mini layout-fixed">
	<?php require_once("../menu_lateral.php");
    if (!PermisosHelpers::verficarAcceso( Functions::getNameDirectory() )) {
        include ('../errorNoTienePermisos.php');
    }
    else { ?>
        <!-- BOX COMPLETO DE LA VISTA -->
        <div class="content-wrapper">
            <!-- BOX DE LA MIGA DE PAN -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h2>Inventario Global</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                                <li class="breadcrumb-item active">Inventario Global</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <!-- BOX DEL CONTENIDO DE LA VISTA FORMULARIO Y TABLA -->
            <section class="content">
                <!-- BOX FORMULARIO -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Seleccione los Almacenes</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fas fa-minus"></i></button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <!-- BOX CARD QUE CONTIENE EL FORMULARIO QUE SE CIERRA -->
                    <div class="card-body" id="minimizar">
                        <form id="frminventario" class="form-horizontal">
                            <div class="form-group col-sm-4 select2-blue">
                                <label>Almacen</label>
                                <select class="select2 depo" name="depo[]" id="depo[]" multiple="multiple" data-placeholder="Seleccione Almacen" data-dropdown-css-class="select2-blue" style="width: 100%;" required>
                                    <!-- la lista de almacenes se carga por ajax -->
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <input type="checkbox" id="checkbox"> Seleccionar Todos
                            </div>
                        </form>

                    </div>
                    <!-- BOX BOTON DE PROCESO -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success" id="btn_inventarioglobal"><i class="fa fa-search" aria-hidden="true"></i> Consultar</button>
                    </div>
                </div>
                <!-- BOX TABLA -->
                <div class="card card-info" id="tabla">
                    <div class="card-header">
                        <h3 class="card-title">Inventario Global</h3>
                    </div>
                    <div class="card-body" style="width:auto;">
                        <table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="inventarioglobal_data">
                            <thead style="background-color: #17A2B8;color: white;">
                                <tr>
                                    <th class="text-center" title="#">#</th>
                                    <th class="text-center" title="Codigo">Codigo</th>
                                    <th class="text-center" title="Producto">Producto</th>
                                    <th class="text-center" title="Cantidad Bultos Por Despachar">Cantidad Bultos por Despachar</th>
                                    <th class="text-center" title="Cantidad Paquetes por Despachar">Cantidad Paquetes por Despachar</th>
                                    <th class="text-center" title="Cantidad Bultos Sistema">Cantidad Bultos Sistema</th>
                                    <th class="text-center" title="Canidad Paquetes Sistema">Canidad Paquetes Sistema</th>
                                    <th class="text-center" title="Total Inventario Bultos">Total Inventario Bultos</th>
                                    <th class="text-center" title="Total Inventario Paquetes">Total Inventario Paquetes</th>
                                </tr>
                            </thead>
                            <tfoot style="background-color: #ccc;color: white;">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th colspan="2" class="text-right">Total=</th>
                                    <th class="text-center" id="tfoot_cantbul_x_des">Cantidad Bultos por Despachar</th>
                                    <th class="text-center" id="tfoot_cantpaq_x_des">Cantidad Paquetes por Despachar</th>
                                    <th class="text-center" id="tfoot_cantbul_sistema">Cantidad Bultos Sistema</th>
                                    <th class="text-center" id="tfoot_cantpaq_sistema">Cantidad Paquetes Sistema</th>
                                    <th class="text-center" id="tfoot_totalbul_inv">Total Inventario Bultos</th>
                                    <th class="text-center" id="tfoot_totalpaq_inv">Total Inventario Paquetes</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                <!-- TD TABLA LLEGAN POR AJAX -->
                            </tbody>
                        </table>
                        <!-- BOX BOTONES DE REPORTES-->
                        <div align="center">
                            <br>
                            <p id="cuenta"></p>
                            <br>
                        </div>
                        <div align="center">
                            <button type="button" class="btn btn-info" id="btn_excel">Exportar a Excel</button>
                            <button type="button" class="btn btn-info" id="btn_pdf">Exportar a PDF</button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php require_once("../footer.php"); ?>
        <script type="text/javascript" src="inventarioglobal.js"></script><?php
    }
    ?>
</body>
</html>