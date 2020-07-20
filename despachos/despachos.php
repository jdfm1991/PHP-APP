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
						<h2>Creación de Despachos</h2>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
							<li class="breadcrumb-item active">Creación de Despacho</li>
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
					<h3 class="card-title">Formulario para la Creación del Despacho</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fas fa-minus"></i></button>
						<button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove"><i class="fas fa-times"></i></button>
					</div>
				</div>
				<!-- BOX CARD QUE CONTIENE EL FORMULARIO QUE SE CIERRA -->
				<div class="card-body" id="minimizar">
					<div class="stepwizard">
						<div class="stepwizard-row setup-panel">
							<div class="stepwizard-step">
								<a href="#step-1" type="button" class="btn btn-primary btn-circle" id="step1">1</a>
								<p>Paso 1</p>
							</div>
							<div class="stepwizard-step">
								<a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
								<p>Paso 2</p>
							</div>

						</div>
					</div>
					<form role="form">
						<div class="row setup-content" id="step-1">

							<div class="col-md-12">
								<h3> Datos de Translado</h3>
								<label>Fecha del Despacho</label>
								<input type="date" class="form-control" id="fecha" name="fecha" >
								<label>Chofer</label>
								<select class="form-control custom-select" id="chofer" name="chofer" style="width: 100%;" >
									<!-- los choferes se cargan por ajax -->
								</select>
								<label>Vehiculo</label>
								<select class="form-control custom-select" id="vehiculo" name="vehiculo" style="width: 100%;" >
									<!-- los vehiculos se cargan por ajax -->
								</select>
								<label>Destino</label>
								<input type="text" class="form-control input-sm" maxlength="120" id="destino" name="destino" >
								<br />
								<button class="btn btn-primary pull-left verFactura" id="buscarxfact_button"  onclick="limpiar_campo_factura_modal()" data-toggle="modal" data-target="#buscarxfacturaModal" type="button">Ver Factura</button>
                                <button class="btn btn-primary porDespachar" type="button">Por Despachar</button>

								<button class="btn btn-success nextBtn  float-right" type="button">Siguiente</button>
							</div>

						</div>
						<div class="row setup-content" id="step-2">

							<div class="col-md-12">
								<h3> Inclusión de Facturas</h3>
								<div class="form-group">
									<label class="control-label">Ingrese Número de Factura a Despachar</label>
									<input maxlength="10" type="text" class="form-control" placeholder="Numero de Factura" id="factura" name="factura" onkeypress="onPressKey(event)" />
								</div>
								<button class="btn btn-primary pull-left anadir" type="button">Añadir</button>

                                <div class="form-check form-check-inline container text-center  justify-content-center align-items-center" id="containerProgress">
                                    <div class="progress progress-xs" style="width: 10em">
                                        <div class="progress-bar bg-success" id="barraProgreso" style="width: 0%"></div>
                                    </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <h5><span class="badge bg-success" id="textoBarraProgreso">0 / 0</span></h5>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <div class="progress progress-xs" style="width: 10em">
                                        <div class="progress-bar bg-info" id="barraProgresoCubicaje" style="width: 0%"></div>
                                    </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <h5><span class="badge bg-info" id="textoBarraProgresoCubicaje">0 / 0</span></h5>
                                </div>

								<button class="btn btn-success float-right generar" type="button">Generar!</button>
							</div>

						</div>

					</form>
				</div>
				<!-- BOX  LOADER -->
                <figure id="loader1">
                    <div class="dot white"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </figure>
			</div>
			<div class="card card-info" id="tabla_facturas_por_despachar">
				<div class="card-header">
					<h3 class="card-title">Relación de Facturas a Despachar</h3>
				</div>
				<div class="card-body" style="width:auto;">
					<table class="table table-hover table-condensed table-bordered table-striped" style="width:100%;" id="fact_por_despachar_data">
						<thead style="background-color: #17A2B8;color: white;">
							<tr>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Factura">Factura</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Fecha">Fecha</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Razón Social">Razón Social</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Zona">Zona</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Ruta O EDV">Ruta</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Monto">Monto</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Peso (Kg)">Peso (Kg)</th>
                                <th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Volumen (cm3)">Volumen (cm3)</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Acción">Acción</th>
							</tr>
						</thead>
						<tfoot style="background-color: #ccc;color: white;">
							<tr>
								<th style="text-align: center;">Factura</th>
								<th style="text-align: center;">Fecha</th>
								<th style="text-align: center;">Razón Social</th>
								<th style="text-align: center;">Zona</th>
								<th style="text-align: center;">Ruta</th>
								<th style="text-align: center;">Monto</th>
								<th style="text-align: center;">Peso (Kg)</th>
                                <th style="text-align: center;">Volulem (cm3)</th>
								<th style="text-align: center;">Acción</th>
							</tr>
						</tfoot>
						<tbody>
							<!-- TD TABLA LLEGAN POR AJAX -->
						</tbody>
					</table>

				</div>
			</div>
            <input name="ci_usuario" id="ci_usuario" value="<?php echo $_SESSION["cedula"]?>" type="hidden" />
			<div class="card card-info" id="tabla_detalle_despacho" style="display:none;">
				<div class="card-header">
					<h3 class="card-title">Detalle del Despacho</h3>
				</div>
				<div class="card-body" style="width:auto;">
					<table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="despacho_general_data">
						<thead style="background-color: #17A2B8;color: white;">
							<tr>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Codigo del Producto">Codigo del Producto</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Descripción">Descripción</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Cantidad de Bultos">Cantidad de Bultos</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Cantidad de Paquetes">Cantidad de Paquetes</th>
							</tr>
						</thead>
						<tfoot style="background-color: #aaa;color: white;">
							<tr>
								<th style="text-align: center;"></th>
								<th style="text-align: center;">TOTAL = </th>
								<th id="cantBul_tfoot" style="text-align: center;">Cantidad de Bultos</th>
								<th id="cantPaq_tfoot" style="text-align: center;">Cantidad de Paquetes</th>
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
						<button type="button" class="btn btn-info" id="btn_newdespacho">Crear Otro Despacho</button>
						<button type="button" class="btn btn-info" id="btn_pdf">Exportar a PDF</button>
					</div>
				</div>
			</section>

        <!-- Modal bucar factura -->
        <?php include 'modales/buscar_factura_modal.html' ?>

    </div>
    <?php require_once("../footer.php"); ?>
    <script type="text/javascript" src="despachos.js"></script>
</body>
</html>
