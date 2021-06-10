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
<?php require_once("../header.php");?>
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
                            <h2>Historico Costos</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                                <li class="breadcrumb-item active">Historico Costos</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <!-- BOX DEL CONTENIDO DE LA VISTA FORMULARIO Y TABLA -->
            <section class="content">
                <!-- BOX FORMULARIO -->
                <div class="card card-info"  >
                    <div class="card-header">
                        <h3 class="card-title">Seleccione rango de fecha</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fas fa-minus"></i></button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <!-- BOX CARD QUE CONTIENE EL FORMULARIO QUE SE CIERRA -->
                    <div  class="card-body" id="minimizar">
                        <form class="form-horizontal" >
                            <div class="form-group row">
                                <div class="form-group col-sm-2">
                                    <label for="fechai">Desde</label>
                                    <input type="date" class="form-control" id="fechai" name="fechai" required>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for="fechaf">Hasta</label>
                                    <input type="date" class="form-control" id="fechaf" name="fechaf" required>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- BOX BOTON DE PROCESO -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success" id="btn_consultar"><i class="fa fa-search" aria-hidden="true"></i> Consultar</button>
                    </div>
                </div>

                <!-- BOX TABLA -->
                <div class="card card-info" id="tabla">
                    <div class="card-header">
                        <h3 class="card-title">Historico Costos</h3>
                    </div>
                    <div class="card-body" style="width:auto;">
                        <table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="historicocostos_data">
                            <thead style="background-color: #17A2B8;color: white;">
                            <tr>
                                <th class="text-center" title="#">#</th>
                                <th class="text-center" title="Codigo Producto">Codigo Producto</th>
                                <th class="text-center" title="Descripción">Descripción</th>
                                <th class="text-center" title="Marca">Marca</th>
                                <th class="text-center" title="Fecha">Fecha</th>
                                <th class="text-center" title="Costos">Costos</th>
                                <th class="text-center" title="Cantidad">Cantidad</th>
                            </tr>
                            </thead>
                            <tfoot style="background-color: #ccc;color: white;">
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Codigo Producto</th>
                                <th class="text-center">Descripción</th>
                                <th class="text-center">Marca</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Costos</th>
                                <th class="text-center">Cantidad</th>
                            </tr>
                            </tfoot>
                            <tbody>
                            <!-- TD TABLA LLEGAN POR AJAX -->
                            </tbody>
                        </table>
                        <!-- BOX BOTONES DE REPORTES-->
                        <div align="center">
                            <br><p><span id="total_registros"></span></p><br>
                            <button type="button" class="btn btn-info" id="btn_excel">Exportar a Excel</button>
                            <button type="button" class="btn btn-info" id="btn_pdf">Exportar a PDF</button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php require_once("../footer.php");?>
        <script type="text/javascript" src="historicocostos.js"></script><?php
    }
    ?>
</body>
</html>
