<?php
//LLAMAMOS A LA CONEXION.
//LLAMAMOS A LAS CONSTANTES.
require_once("../acceso/conexion.php");
require_once("../acceso/const.php");
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
                            <label>Desde</label>
                            <input type="date" class="form-control" id="fechai" name="fechai" required>
                        </div>
                        <div class="form-group col-sm-2">
                            <label>Hasta</label>
                            <input type="date" class="form-control" id="fechaf" name="fechaf" required>
                        </div>
                        <div class="form-group col-sm-2">
                            <label>Tipo</label>
                            <select class="form-control custom-select" name="tipo" id="tipo" style="width: 100%;"
                                    required>
                                <!-- la lista de tipo se carga por ajax -->
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="form-group col-sm-6">
                            <select class="custom-select" name="vendedores" id="vendedores" style="width: 100%;"
                                    required>
                                <!-- la lista de marcas se carga por ajax -->
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
                <h3 class="card-title">Relación de Facturas sin despachar</h3>
            </div>
            <div class="card-body" style="width:auto;">
                <table class="table table-hover table-condensed table-bordered table-striped" style="width:100%;" id="tablafactsindes">
                    <thead style="background-color: #17A2B8;color: white;">
                        <tr>
                            <th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Fact">Documento</th>
                            <th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="FechaEmi">Fecha Emisión</th>

                            <th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="FechaDesp">Fecha Despacho</th>
                            <th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="DiasTrans">Dias Transcurridos</th>
                            <th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Código">Código</th>
                            <th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Cliente">Cliente</th>
                            <th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="DíasHastHoy">Días Transcurridos Hasta Hoy</th>
                            <th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="CantBult">Cantidad Bultos</th>
                            <th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="CantPaq">Cantidad Paquetes</th>
                            <th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Monto">Monto Bs</th>
                            <th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="EDV">EDV</th>

                            <th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="TPromEsti">Tiempo Promedio Estimado</th>
                            <th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="%Oportunidad">%Oportunidad</th>
                        </tr>
                    </thead>
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

