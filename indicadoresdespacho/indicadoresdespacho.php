<?php
//LLAMAMOS A LA CONEXION.
//LLAMAMOS A LAS CONSTANTES.
require_once("../acceso/conexion.php");
require_once("../acceso/const.php");
?>
<!DOCTYPE html>
<html>
<?php require_once("../header.php");?>
<body class="hold-transition sidebar-mini layout-fixed">
<?php require_once("../menu_lateral.php");?>
<!-- BOX COMPLETO DE LA VISTA -->
<div class="content-wrapper">
    <!-- BOX DE LA MIGA DE PAN -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h2>Indicadores de Gestión de Despachos</h2>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Indicadores de Gestión de Despachos</li>
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
                <h3 class="card-title">Seleccione</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fas fa-minus"></i></button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <!-- BOX CARD QUE CONTIENE EL FORMULARIO QUE SE CIERRA -->
            <div  class="card-body" id="minimizar">
                <ul class="nav nav-pills mb-3 " id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-fectivas-tab" data-toggle="pill" href="#pills-efectivas" role="tab" aria-selected="true">
                            Entregas Efectivas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-rechazo-tab" data-toggle="pill" href="#pills-rechazo" role="tab" aria-selected="false">
                            Rechazo de los Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-oportunidad-tab" data-toggle="pill" href="#pills-oportunidad" role="tab" aria-selected="false">
                            Oportunidad de Despacho
                        </a>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-efectivas" role="tabpanel">
                        <form id="efectivas_form" class="form-horizontal" method="post">
                            <div class="form-group row">
                                <div class="form-group col-sm-2">
                                    <label for="fechai">Desde</label>
                                    <input type="date" class="form-control" id="fechai" name="fechai" required>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for="fechaf">Hasta</label>
                                    <input type="date" class="form-control" id="fechaf" name="fechaf" required>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for="chofer">Chofer</label>
                                    <select class="form-control custom-select" name="chofer" id="chofer" style="width: 100%;" required>
                                        <!-- la lista de tipo se carga por ajax -->
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="pills-rechazo" role="tabpanel">
                        <form id="rechazo_form" class="form-horizontal" method="post">
                            <div class="form-group row">
                                <div class="form-group col-sm-2">
                                    <label for="fechai">Desde</label>
                                    <input type="date" class="form-control" id="fechai" name="fechai" required>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for="fechaf">Hasta</label>
                                    <input type="date" class="form-control" id="fechaf" name="fechaf" required>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for="chofer">Chofer</label>
                                    <select class="form-control custom-select" name="chofer" id="chofer" style="width: 100%;" required>
                                        <!-- la lista de tipo se carga por ajax -->
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="form-group col-sm-6">
                                    <select class="custom-select" name="causa" id="causa" style="width: 100%;" required>
                                        <!-- la lista de casusas de rechazo se carga por ajax -->
                                        <option value="">Seleccione Causa del rechazo</option>
                                        <option value="Todos">Todos</option>
                                        <option value="Merc. no solicitada">Merc. no solicitada</option>
                                        <option value="Fecha venc. cercana">Fecha venc. cercana</option>
                                        <option value="EDV no informo mod pago">EDV no informo mod pago</option>
                                        <option value="Cliente no puede pagar">Cliente no puede pagar</option>
                                        <option value="Cliente indisponible para recepcion">Cliente indisponible para recepcion</option>
                                        <option value="Precio no fue el acordado">Precio no fue el acordado</option>
                                        <option value="Mercancia vencida">Mercancia vencida</option>
                                        <option value="Pedido incompleto">Pedido incompleto</option>
                                        <option value="Faltante en el almacen">Faltante en el almacen</option>
                                        <option value="Faltante en el bulto">Faltante en el bulto</option>
                                        <option value="Caja mal estado">Caja mal estado</option>
                                        <option value="Retraso de entrega">Retraso de entrega</option>
                                        <option value="Facturado bajo cero">Facturado bajo cero</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="pills-oportunidad" role="tabpanel">
                        <form id="oportunidad_form" class="form-horizontal" method="post">
                            <div class="form-group row">
                                <div class="form-group col-sm-2">
                                    <label for="fechai">Desde</label>
                                    <input type="date" class="form-control" id="fechai" name="fechai" required>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for="fechaf">Hasta</label>
                                    <input type="date" class="form-control" id="fechaf" name="fechaf" required>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for="chofer">Chofer</label>
                                    <select class="form-control custom-select" name="chofer" id="chofer" style="width: 100%;" required>
                                        <!-- la lista de tipo se carga por ajax -->
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- BOX BOTON DE PROCESO -->
            <div class="card-footer">
                <button type="submit" class="btn btn-success" id="btn_consultar"><i class="fa fa-search" aria-hidden="true"></i> Consultar</button>
            </div>
        </div>
        <!-- BOX  LOADER -->
        <figure id="loader">
            <div class="dot white"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </figure>

        <div class="row">
            <div class="col-md-6">
                <!-- BOX TABLA -->
                <div class="card card-info" id="tabla">
                    <div class="card-header">
                        <h3 class="card-title"><span class="title-card"></span></h3>
                    </div>
                    <div class="card-body" style="width:auto;">
                        <div class="row">
                            <div class="col">
                                <div class="form-check form-check-inline">
                                    <label class="pt-1">CHOFER: </label>&nbsp;&nbsp;&nbsp;
                                    <input type="text" class="form-control" id="datos_chofer" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row pt-3">
                            <table class="table table-hover table-condensed table-bordered table-striped" style="width:100%;" id="indicadores_data">
                                <thead style="background-color: #17A2B8;color: white;">
                                <tr>
                                    <th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="F. Entreg">Fecha Entrega</th>
                                    <th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="P. Despachados">Pedidos Despachados</th>
                                    <th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="% Efectividad">% Efectividad</th>
                                    <th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Orden(es) D">Orden(es) Despacho</th>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- TD TABLA LLEGAN POR AJAX -->
                                </tbody>
                            </table>
                        </div>

                        <div class="row pt-3">
                            <div class="col">
                                <p>
                                    Total de Pedidos en el camión: &nbsp;&nbsp;&nbsp;&nbsp;<label id="total_ped_camion"></label>
                                </p>
                                <p>
                                    Pedidos pendientes por liquidar: <label id="total_ped_pendiente"></label>
                                </p>
                            </div>
                            <div class="col">
                                <p>
                                    Total de Pedidos entregados: &nbsp;&nbsp;&nbsp;&nbsp;<label id="total_ped_entregados"></label>
                                </p>
                                <p>
                                    Promedio Diario de Despachos: <label id="promedio_diario_despachos"></label>
                                </p>
                            </div>
                        </div>
                        <!-- BOX BOTONES DE REPORTES-->
                        <!--<div align="center">
                            <br><p><span id="total_registros"></span></p><br>
                            <button type="button" class="btn btn-info" id="btn_excel">Exportar a Excel</button>
                            <button type="button" class="btn btn-info" id="btn_pdf">Exportar a PDF</button>
                        </div>-->
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <!-- BAR CHART -->
                <div class="card card-info" id="grafico">
                    <div class="card-header">
                        <h3 class="card-title">Grafico</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group row" align="center">
                            <div class="form-group col-sm-5">
                                <input type="date" class="form-control" id="fechai_disabled" name="fechai" disabled>
                            </div>
                            <div class="form-group col-sm-2 pt-1" align="center">
                                <label> AL </label>
                            </div>
                            <div class="form-group col-sm-5">
                                <input type="date" class="form-control" id="fechaf_disabled" name="fechaf" disabled>
                            </div>
                        </div>

                        <div class="chart">
                            <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>

                        <div class="pt-3">
                            <div class="form-group">
                                <label id="ordenes_label">ORDENES DE DESPACHO</label>
                                <p id="ordenes_despacho"></p>
                            </div>
                            <div class="form-group">
                                <label id="fact_label">FACTURAS SIN LIQUIDAR</label>
                                <p id="fact_sinliquidar"></p>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </section>
</div>
<?php require_once("../footer.php");?>
<!-- ChartJS -->
<script src="<?php echo SERVERURL; ?>public/plugins/chart.js/Chart.min.js"></script>

<script type="text/javascript" src="indicadoresdespacho.js"></script>

<script type="text/javascript" src="indicadores_entregas_efectivas.js"></script>
<script type="text/javascript" src="indicadores_causas_rechazo.js"></script>
<script type="text/javascript" src="indicadores_oportunidad_despacho.js"></script>
</body>
</html>
