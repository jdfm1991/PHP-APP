<?php
//LLAMAMOS A LA CONEXION.
//LLAMAMOS A LAS CONSTANTES.
require_once("../acceso/conexion.php");
require_once("../acceso/const.php");
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once("../header.php"); ?>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
        <div class="container">
            <a href="#" class="navbar-brand">
                <img src="<?php echo SERVERURL; ?>public/dist/img/AdminLTELogo.png " alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                     style="opacity: .8">
                <span class="brand-text font-weight-light">Logística y Despacho</span>
            </a>

            <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Right navbar links -->
            <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                <li class="nav-item">
                    <a href="#" class="btn btn-primary">Cerrar Ventana</a>
                </li>
            </ul>
        </div>
    </nav>
    <!-- /.navbar -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark"> KPI <small>(Key Performance Indicator)</small></h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Inicio</li>
                            <li class="breadcrumb-item">KPI</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
                <div class="row mb-2">
                    <div class="col-sm-2">
                            <dt class="col-sm-4 text-gray">Desde:</dt>
                            <dd class="col-sm-8 text-gray">01/01/2020</dd>
                    </div><!-- /.col -->
                    <div class="col-sm-2">
                        <dt class="col-sm-4 text-gray">Hasta:</dt>
                        <dd class="col-sm-8 text-gray">31/01/2020</dd>
                    </div><!-- /.col -->
                    <div class="col-sm-3">
                        <dt class="col-sm-4 text-gray">Días Habiles:</dt>
                        <dd class="col-sm-8 text-gray">21</dd>
                    </div><!-- /.col -->
                    <div class="col-sm-3">
                        <dt class="col-sm-4 text-gray">Días Transc:</dt>
                        <dd class="col-sm-8 text-gray">15</dd>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container">
                <table id="tabla" class="table table-sm text-center table-hover table-condensed table-bordered table-striped" style="width:100%;">
                    <thead style="background-color: #17A2B8;color: white;">
                    <tr>
                        <th class="small align-middle">Rutas</th>
                        <th class="small align-middle">Maestro</th>
                        <th class="small align-middle">Clientes Activados</th>
                        <!--marcas-->
                        <th style="width: 20px;"><div class="small align-middle" style="width: 10px; word-wrap: break-word; text-align: center">FLORESTAL</div></th>
                        <th style="width: 20px;"><div class="small align-middle" style="width: 10px; word-wrap: break-word; text-align: center">ST MORITZ</div></th>
                        <th style="width: 20px;"><div class="small align-middle" style="width: 10px; word-wrap: break-word; text-align: center">PEPSICO</div></th>
                        <th style="width: 20px;"><div class="small align-middle" style="width: 10px; word-wrap: break-word; text-align: center">LA PASTORENA</div></th>
                        <th style="width: 20px;"><div class="small align-middle" style="width: 10px; word-wrap: break-word; text-align: center">IBERIA</div></th>
                        <th style="width: 20px;"><div class="small align-middle" style="width: 10px; word-wrap: break-word; text-align: center">PUIG</div></th>
                        <th style="width: 20px;"><div class="small align-middle" style="width: 10px; word-wrap: break-word; text-align: center">GENICA</div></th>
                        <th style="width: 20px;"><div class="small align-middle" style="width: 10px; word-wrap: break-word; text-align: center">BARBANESA</div></th>
                        <th style="width: 20px;"><div class="small align-middle" style="width: 10px; word-wrap: break-word; text-align: center">COMETIN</div></th>
                        <th style="width: 20px;"><div class="small align-middle" style="width: 10px; word-wrap: break-word; text-align: center">CHARLIZE</div></th>
                        <!--marcas-->
                        <th class="small align-middle">% Activación Alcanzado</th>
                        <th class="small align-middle">Pendiente</th>
                        <th class="small align-middle">Visita</th>
                        <th class="small align-middle">Objetivo Facturas más Notas Mensual</th>
                        <th class="small align-middle">Total Facturas Realizadas</th>
                        <th class="small align-middle">Total Notas Realizadas</th>
                        <th class="small align-middle">Devoluciones Realizadas (nt + fac)</th>
                        <th class="small align-middle">Total Devoluciones Realizadas ($)</th>
                        <th class="small align-middle">Tiempo Promedio Estimado</th>
                        <th class="small align-middle">%Oportunidad</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- TD TABLA LLEGAN POR AJAX -->
                    </tbody>
                </table>


                <hr>

                <a href="#" class="card-link" id="btn_excel">Exportar a Excel</a>
                <a href="#" class="card-link" id="btn_pdf">Exportar a PDF</a>
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- MODAL  DETALLE DE FACTURA -->
    <?php //include 'modales/detalle_factura.html' ?>

    <!-- Main Footer -->
    <?php require_once("../footer.php"); ?>
    <script type="text/javascript" src="kpi_tabla.js"></script>
</div>
<!-- ./wrapper -->

</body>
</html>


