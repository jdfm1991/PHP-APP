<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");
?>
<!DOCTYPE html>
<html>
<?php require_once("../header.php"); ?>
<body class="hold-transition sidebar-mini layout-fixed">
<?php require_once("../menu_lateral.php"); ?>
<!-- BOX COMPLETO DE LA VISTA -->
<div class="content-wrapper">
    <!-- BOX DE LA MIGA DE PAN -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h2>Facturas por Despachar</h2>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Facturas por Despachar</li>
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
                <h3 class="card-title">Seleccione las Siguientes Opciones</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fas fa-minus"></i></button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <!-- BOX CARD QUE CONTIENE EL FORMULARIO QUE SE CIERRA -->
            <div class="card-body" id="minimizar">
                <form class="form-horizontal" id="frmfactsindes">
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
                            <label for="tipo">Tipo</label>
                            <select class="form-control custom-select" name="tipo" id="tipo" style="width: 100%;" required>
                                <!-- la lista de tipo se carga por ajax -->
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="form-group col-sm-6">
                            <select class="custom-select" name="vendedores" id="vendedores" style="width: 100%;" required>
                                <!-- la lista de vendedores se carga por ajax -->
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="checkbox" value="checkbox"
                                   name="checkbox">
                            <label for="checkbox" class="custom-control-label">Ver Despachadas</label>
                        </div>
                    </div>
                </form>
            </div>
            <!-- BOX BOTON DE PROCESO -->
            <div class="card-footer">
                <button type="submit" class="btn btn-success" id="btn_factsindes"><i class="fa fa-search" aria-hidden="true"></i> Consultar
                </button>
            </div>
        </div>
        <!-- BOX TABLA -->
        <div class="card card-info" id="tabla">
            <div class="card-header">
                <h3 class="card-title">Relación de Facturas sin despachar</h3>
            </div>
            <div class="card-body" style="width:auto;">
                <table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="tablafactsindes">
                    <thead style="background-color: #17A2B8;color: white;">
                        <tr>
                            <th class="text-center" title="Fact">Documento</th>
                            <th class="text-center" title="FechaEmi">Fecha Emisión</th>

                            <th class="text-center" title="FechaDesp">Fecha Despacho</th>
                            <th class="text-center" title="DiasTrans">Dias Transcurridos</th>
                            <th class="text-center" title="Código">Código</th>
                            <th class="text-center" title="Cliente">Cliente</th>
                            <th class="text-center" title="DíasHastHoy">Días Transcurridos Hasta Hoy</th>
                            <th class="text-center" title="CantBult">Cantidad Bultos</th>
                            <th class="text-center" title="CantPaq">Cantidad Paquetes</th>
                            <th class="text-center" title="Monto">Monto Bs</th>
                            <th class="text-center" title="EDV">EDV</th>

                            <th class="text-center" title="TPromEsti">Tiempo Promedio Estimado</th>
                            <th class="text-center" title="%Oportunidad">%Oportunidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- TD TABLA LLEGAN POR AJAX -->
                    </tbody>
                </table>
                <!-- BOX BOTONES DE REPORTES-->
                <div align="center"><br>
                    <br<p><span id="total_registros"></span></p><br>
                    <button type="button" class="btn btn-info" id="btn_excel">Exportar a Excel</button>
                    <button type="button" class="btn btn-info" id="btn_pdf">Exportar a PDF</button>
                </div>
            </div>
        </div>

        </div>
    </section>

    <!-- MODAL  DETALLE DE FACTURA -->
    <?php include 'modales/detalle_factura.html' ?>

</div>
<?php require_once("../footer.php"); ?>
<script type="text/javascript" src="facturassindespachar.js"></script>
</body>
</html>

