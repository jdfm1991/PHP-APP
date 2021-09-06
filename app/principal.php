<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
require_once("../config/conexion.php");
?>
<!DOCTYPE html>
<html>
<?php require_once("header.php"); ?>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php require_once("menu_lateral.php"); ?>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <!-- Loading (remove the following to stop the loading)-->
                            <div id="loader_docPorDespachar" class="overlay dark">
                                <i class="fas fa-3x fa-sync-alt"></i>
                            </div>

                            <div class="inner">
                                <h3>
                                    <span id="docPorDespachar">0</span>
                                </h3>
                                <p>Documentos por Despachar</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-ios-copy"></i>
                            </div>
                            <a href="#" class="small-box-footer"><?=Strings::titleFromJson('boton_ver_reporte')?><i
                                        class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <!-- Loading (remove the following to stop the loading)-->
                            <div id="loader_pedPorFacturar" class="overlay dark">
                                <i class="fas fa-3x fa-sync-alt"></i>
                            </div>

                            <div class="inner">
                                <h3>
                                    <span id="pedsPorFacturar">0</span>
                                </h3>
                                <p>Pedidos por facturar</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="#" class="small-box-footer"><?=Strings::titleFromJson('boton_ver_reporte')?><i
                                        class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <!-- Loading (remove the following to stop the loading)-->
                            <div id="loader_cxc" class="overlay dark">
                                <i class="fas fa-3x fa-sync-alt"></i>
                            </div>

                            <div class="inner">
                                <h5 style="font-weight: 700">
                                    <span id="cxc_in_bs">0,0</span><sup style="font-size: 16px">BS</sup>
                                    <br>
                                    <span id="cxc_in_dolar">0,0</span><sup style="font-size: 16px">$</sup>

                                </h5>

                                <p>Cuentas por Cobrar</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-cash"></i>
                            </div>
                            <a href="#" class="small-box-footer"><?=Strings::titleFromJson('boton_ver_reporte')?><i
                                        class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <!-- Loading (remove the following to stop the loading)-->
                            <div id="loader_cxp" class="overlay dark">
                                <i class="fas fa-3x fa-sync-alt"></i>
                            </div>

                            <div class="inner">
                                <h5 style="font-weight: 700">
                                    <span id="cxp_in_bs">0,0</span><sup style="font-size: 16px">BS</sup>
                                    <br>
                                    <span id="cxp_in_dolar">0,0</span><sup style="font-size: 16px">$</sup>
                                </h5>
                                <p>Cuentas por Pagar</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-cash"></i>
                            </div>
                            <a href="#" class="small-box-footer"><?=Strings::titleFromJson('boton_ver_reporte')?><i
                                        class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <!-- Loading (remove the following to stop the loading)-->
                            <div id="loader_clientes_n" class="overlay dark">
                                <i class="fas fa-3x fa-sync-alt"></i>
                            </div>

                            <span class="info-box-icon bg-info"><i class="far fa-user"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Clientes Naturales</span>
                                <span id="cliente_n" class="info-box-number">0</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">

                            <!-- Loading (remove the following to stop the loading)-->
                            <div id="loader_clientes_j" class="overlay dark">
                                <i class="fas fa-3x fa-sync-alt"></i>
                            </div>

                            <span class="info-box-icon bg-info"><i class="fa fa-user-tie"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Clientes Jurídicos</span>
                                <span id="cliente_j" class="info-box-number">0</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="far fa-copy"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Uploads</span>
                                <span class="info-box-number">13,648</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger"><i class="far fa-star"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Likes</span>
                                <span class="info-box-number">93,139</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header border-0">
                                <div class="d-flex justify-content-between">
                                    <h3 class="card-title">Ventas</h3>
                                    <a href="javascript:void(0);">Ver Reporte</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex">

                                    <p class="d-flex flex-column">
                                        <span class="text-bold text-lg">$18,230.00</span>
                                        <span>Ventas a lo largo del tiempo</span>
                                    </p>
                                    <!--<p class="ml-auto d-flex flex-column text-right">
                                        <span class="text-success">
                                            <i class="fas fa-arrow-up"></i> 33.1%
                                        </span>
                                        <span class="text-muted">Desde el mes pasado</span>
                                    </p>-->
                                </div>
                                <div class="position-relative mb-4">
                                    <canvas id="sales-chart" height="200"></canvas>
                                </div>

                                <div class="d-flex flex-row justify-content-end">
                                    <span class="mr-2"><i class="fas fa-square text-primary"></i> Este Año</span>
                                    <span><i class="fas fa-square text-gray"></i> El año pasado</span>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header border-0">
                                <h3 class="card-title">Valorización de Inventario</h3>
                            </div>
                            <div class="card-body table-responsive p-0" style="height: 350px;">
                                <table id="inventario_valorizado" class="table table-striped table-valign-middle table-head-fixed text-nowrap text-center">
                                    <thead>
                                    <tr>
                                        <th><?=Strings::titleFromJson('ubicacion')?></th>
                                        <th><?=Strings::titleFromJson('valoracion')?></th>
                                        <th><?=Strings::titleFromJson('detalle')?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <!--el contenido llega por ajax-->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php require_once("footer.php"); ?>
</div>

<!-- ChartJS -->
<script src="<?php echo URL_LIBRARY; ?>plugins/chart.js/Chart.min.js"></script>
<!-- Custom -->
<script type="text/javascript" src="principal/grafico.js"></script>
<script type="text/javascript" src="principal/principal.js"></script>
</body>

</html>