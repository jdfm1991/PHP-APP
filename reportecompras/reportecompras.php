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
                    <h2>Reporte de compras</h2>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Reporte de compras</li>
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
                <form class="form-horizontal" id="frmCompras">
                    <div class="form-group row">
                        <div class="form-group col-sm-3">
                            <label for="fechai">Fecha</label>
                            <input type="date" class="form-control" id="fechai" name="fechai" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="form-group col-sm-3">
                            <label for="marca">Marca</label>
                            <select class="custom-select" name="marca" id="marca" style="width: 100%;" required>
                                <!-- la lista de marcas se carga por ajax -->
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <!-- BOX BOTON DE PROCESO -->
            <div class="card-footer">
                <button type="submit" class="btn btn-success" id="btn_reportecompra"><i class="fa fa-search" aria-hidden="true"></i> Consultar</button>
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
        <!-- BOX TABLA -->
        <div class="card card-info" id="tabla">
            <div class="card-header">
                <h3 class="card-title">Reporte de compras</h3>
            </div>
            <div class="card-body table-responsive p-2" style="width:auto; height: 500px;">
                <form id="form_reportecompras" method="post">
                    <table class="table table-hover table-condensed table-bordered table-striped" style="width:100%;" id="reportecompras_data">
                        <thead style="background-color: #17A2B8;color: white;">
                        <tr>
                            <th style="width: 10px" rowspan="2">#</th>
                            <th rowspan="2">Codigo</th>
                            <th rowspan="2">Descripción</th>
                            <th rowspan="2">Display x Bulto</th>
                            <th colspan="2">Último precio de compra</th>
                            <th rowspan="2">% RENT</th>
                            <th colspan="2">Fecha penúltima compra</th>
                            <th colspan="2">Fecha última compra</th>
                            <th colspan="4">Ventas mes anterior</th>
                            <th rowspan="2">Venta total último mes</th>
                            <th rowspan="2">Existencia Actual Bultos</th>
                            <th rowspan="2">Días de Inventarios</th>
                            <th rowspan="2">Sugerido</th>
                            <th rowspan="2">Pedido</th>
                        </tr>
                        <tr>
                            <th>Display</th>
                            <th>Bulto</th>
                            <th>Fecha</th>
                            <th>Bultos</th>
                            <th>Fecha</th>
                            <th>Bultos</th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- TD TABLA LLEGAN POR AJAX -->
                        </tbody>
                    </table>
                </form>
            </div>
            <br>
            <!-- BOX BOTONES DE REPORTES-->
            <div align="center">
                <br>
                <br><p id="total_items">Total de Item:<code> <span id="total_registros"></span> </code></p><br>
                <button type="button" class="btn btn-info" id="btn_excel">Exportar a Excel</button>
                <button type="button" class="btn btn-info" id="btn_pdf">Exportar a PDF</button>
                <br>
                <br>
            </div>
        </div>
    </section>
</div>
<?php require_once("../footer.php");?>
<script type="text/javascript" src="reportecompras.js"></script>
</body>
</html>
