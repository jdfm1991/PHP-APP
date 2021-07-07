<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");
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
                <img src="<?php echo URL_LIBRARY; ?>dist/img/AdminLTELogo.png " alt="AdminLTE Logo"
                     class="brand-image img-circle elevation-3"
                     style="opacity: .8">
                <span class="brand-text font-weight-light"><?=Strings::titleFromJson('nombre_app')?></span>
            </a>

            <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                    aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Right navbar links -->
            <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                <li class="nav-item">
                    <input type="button" class="btn btn-primary" value="Cerrar Ventana" onClick="self.close();" onKeyPress="self.close();" />
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
                        <h1 class="m-0 text-dark">Libro de Compras</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Inicio</li>
                            <li class="breadcrumb-item">Libro de Compras</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->

                <div class="row mb-2">
                    <div class="col-sm-3 mt-4 form-check-inline">
                    </div><!-- /.col -->
                    <div class="col-sm-3 mt-4 form-check-inline">
                        <dt class="col-sm-3 text-gray">Desde:</dt>
                        <input type="text" class="form-control-sm col-8 text-center" id="fechai" value="<?php echo $_GET['fechai']; ?>" readonly>
                    </div><!-- /.col -->
                    <div class="col-sm-3 mt-4 form-check-inline">
                        <dt class="col-sm-4 text-gray">Hasta:</dt>
                        <input type="text" class="form-control-sm col-sm-8 text-center" id="fechaf" value="<?php echo $_GET['fechaf']; ?>" readonly>
                    </div><!-- /.col -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <!--            <div class="container">-->
            <table id="tabla" class="table table-sm text-center table-condensed table-bordered table-striped table-responsive table-primary" style="width:100%;">
                <thead style="color: white; font-weight: bold">
                <tr id="cells">
                    <th class="small align-middle">Nro. Ope</th>
                    <th class="small align-middle">Fecha Documento</th>
                    <th class="small align-middle">Rif</th>
                    <th class="small align-middle">Razón Social</th>
                    <th class="small align-middle">Tip. Doc.</th>
                    <th class="small align-middle">Nro. Comprobante Retención</th>
                    <th class="small align-middle">Nro. Documento</th>
                    <th class="small align-middle">Nro. Control</th>
                    <th class="small align-middle">Tipo Transacción</th>
                    <th class="small align-middle">Nro Fac. Afectada</th>
                    <th class="small align-middle">Total Compras</th>
                    <th class="small align-middle">Compras Exentas</th>
                    <th class="small align-middle">Base Imponible</th>
                    <th class="small align-middle">% Alic</th>
                    <th class="small align-middle">Monto IVA</th>
                    <th class="small align-middle">Monto Retenido</th>
                    <th class="small align-middle">% Ret</th>
                    <th class="small align-middle">Fecha Comprobante</th>
                </tr>
                </thead>
                <tbody style="background-color: aliceblue">
                <!-- TD TABLA LLEGAN POR AJAX -->
                </tbody>
            </table>

            <hr>

            <table id="tabla1" class="table table-sm text-center table-condensed table-bordered table-striped table-primary" style="width:40%;">
                <thead style="color: white; font-weight: bold">
                <tr id="cells">
                    <th class="small align-middle">RESUMEN DE CRÉDITOS FISCALES</th>
                    <th class="small align-middle">BASE IMPONIBLE</th>
                    <th class="small align-middle">CRÉDITO FISCAL</th>
                </tr>
                </thead>
                <tbody style="background-color: aliceblue">
                <!-- TD TABLA LLEGAN POR AJAX -->
                </tbody>
            </table>

            <hr>
            <!--</div>-->
            <!-- /.container-fluid -->
            <div class="container">
                <a href="librocompras_excel.php?&fechai=<?php echo $_GET['fechai']; ?> &fechaf=<?php echo $_GET['fechaf']; ?>" class="card-link" id="btn_excel">
                    <?=Strings::titleFromJson('boton_excel')?>
                </a>
            </div>
            <br>
        </div>

        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <input id="id" type="hidden" value="<?php echo $_SESSION['cedula']; ?>"/>
    <!-- Main Footer -->
    <?php require_once("../footer.php"); ?>
    <script src="<?php echo URL_HELPERS_JS; ?>Number.js" type="text/javascript"></script>

    <script type="text/javascript" src="librocompras_tabla.js"></script>
</div>
<!-- ./wrapper -->

</body>
</html>


