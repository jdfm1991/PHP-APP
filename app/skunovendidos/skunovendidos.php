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
                        <h2>Relación de Productos no Vendidos</h2>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                            <li class="breadcrumb-item active">Relación de Productos no Vendidos</li>
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
                    <h3 class="card-title">Seleccione las Siguientes Opciones</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fas fa-minus"></i></button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <!-- BOX CARD QUE CONTIENE EL FORMULARIO QUE SE CIERRA -->
                <div  class="card-body" id="minimizar">
                    <form class="form-horizontal" >
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="form-check form-check-inline">
                                    <label for="vutil" class="col-form-label col-sm-4">Desde</label>
                                    <input type="date" class="form-control col-sm-9"  id="fechai" name="fechai" required>
                                </div>&nbsp;&nbsp;&nbsp;&nbsp;
                                <div class="form-check form-check-inline">
                                    <label for="vutil" class="col-form-label col-sm-4">Hasta</label>
                                    <input type="date" class="form-control col-sm-9"  id="fechaf" name="fechaf" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- BOX BOTON DE PROCESO -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-success" id="btn_buscar"><i class="fa fa-search" aria-hidden="true"></i> Consultar</button>
                </div>
            </div>

            <!-- BOX TABLA -->
            <div class="card card-info" id="tabla">
                <div class="card-header">
                    <h3 class="card-title">Productos no Vendidos</h3>
                </div>
                <div class="card-body" style="width:auto;">
                    <table class="table table-sm table-hover table-condensed table-bordered table-striped table-responsive text-center" style="width:100%;" id="skunovendidos_data">
                        <thead style="background-color: #17A2B8;color: white;">
                        <tr>
                            <th class="text-center" title="#">#</th>
                            <th class="text-center" title="Numero">Numero</th>
                            <th class="text-center" title="CodVend">CodVend</th>
                            <th class="text-center" title="Vendedor">Vendedor</th>
                            <th class="text-center" title="Codigo Cliente">Codigo Cliente</th>
                            <th class="text-center" title="Cliente">Cliente</th>
                            <th class="text-center" title="Codigo Producto">Codigo Producto</th>
                            <th class="text-center" title="Descripcion">Descripcion</th>
                            <th class="text-center" title="Marca">Marca</th>
                            <th class="text-center" title="Unid. Empaque">Unid. Empaque</th>
                            <th class="text-center" title="Cantidad">Cantidad</th>
                            <th class="text-center" title="Sub Total">Sub Total</th>
                            <th class="text-center" title="Inv. Bultos">Inv. Bultos</th>
                            <th class="text-center" title="Inv. Paquetes">Inv. Paquetes</th>
                            <th class="text-center" title="Fecha">Fecha</th>
                        </tr>
                        </thead>
                        <tfoot style="background-color: #ccc;color: white;">
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Numero</th>
                            <th class="text-center">CodVend </th>
                            <th class="text-center">Vendedor</th>
                            <th class="text-center">Codigo Cliente</th>
                            <th class="text-center">Cliente</th>
                            <th class="text-center">Codigo Producto</th>
                            <th class="text-center">Descripcion</th>
                            <th class="text-center">Marca</th>
                            <th class="text-center">Unid. Empaque</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Sub Total</th>
                            <th class="text-center">Inv. Bultos</th>
                            <th class="text-center">Inv. Paquetes</th>
                            <th class="text-center">Fecha</th>

                        </tr>
                        </tfoot>
                        <tbody>
                        <!-- TD TABLA LLEGAN POR AJAX -->
                        </tbody>
                    </table>

                    <!-- BOX BOTONES DE REPORTES-->
                    <div align="center">
                        <button type="button" class="btn btn-info" id="btn_excel">Exportar a Excel</button>
                        <button type="button" class="btn btn-info" id="btn_pdf">Exportar a PDF</button>
                    </div>
                </div>
        </section>
    </div>
<?php require_once("../footer.php");?>
    <script type="text/javascript" src="skunovendidos.js"></script><?php
}
?>
</body>
</html>

