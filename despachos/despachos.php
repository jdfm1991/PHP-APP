<?php
//LLAMAMOS A LA CONEXION.
//LLAMAMOS A LAS CONSTANTES.
require_once("../acceso/conexion.php");
require_once("../acceso/const.php");
require_once("../choferes/choferes_modelo.php");
$choferes = new Choferes();
$lista_choferes = $choferes->get_choferes();
require_once("../vehiculos/vehiculos_modelo.php");
$vehiculo = new Vehiculos();
$lista_vehiculos = $vehiculo->get_vehiculos();
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
			<div class="card card-info"  >
				<div class="card-header">
					<h3 class="card-title">Formulario para la Creación del Despacho</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fas fa-minus"></i></button>
						<button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove"><i class="fas fa-times"></i></button>
					</div>
				</div>
				<!-- BOX CARD QUE CONTIENE EL FORMULARIO QUE SE CIERRA -->
				<div  class="card-body" id="minimizar">
					<div class="stepwizard">
						<div class="stepwizard-row setup-panel">
							<div class="stepwizard-step">
								<a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
								<p>Paso 1</p>
							</div>
							<div class="stepwizard-step">
								<a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
								<p>Paso 2</p>
							</div>
							<div class="stepwizard-step">
								<a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
								<p>Paso 3</p>
							</div>
						</div>
					</div>
					<form role="form">
						<div class="row setup-content" id="step-1">
							<div class="col-xs-12 col-center">
								<div class="col-md-12">
									<h3> Datos de Translado</h3>
									<label>Fecha del Despacho</label>
									<input type="date" class="form-control"  id="fecha" name="fecha" >
									<label>Chofer</label>
									<select class="form-control custom-select" id="chofer" name="chofer" style="width: 100%;" required>
										<option value="">Seleccione</option>
										<?php
										foreach ($lista_choferes as $query) {
											echo '<option value="' . $query['id'] . '">' . $query['Nomper'] . '</option>';} ?>
										</select>
										<label>Vehiculo</label>
										<select class="form-control custom-select" id="vehiculo" name="vehiculo" style="width: 100%;" required>
											<option value="">Seleccione</option>
											<?php
											foreach ($lista_vehiculos as $query) {
												echo '<option value="' . $query['ID'] . '">' . $query['Modelo'] . "&nbsp;&nbsp;" . $query['Capacidad'] . " Kg" . '</option>';} ?>
											</select>
											<label>Destino</label>
											<input type="text" class="form-control input-sm" maxlength="120" id="destino" name="destino" >
											<br />
											<button class="btn btn-primary" type="button pull-left" >Ver Factura</button>
											<button class="btn btn-primary" type="button" >Por Despachar</button>
											<button class="btn btn-success float-right" type="button" >Siguiente</button>
										</div>
									</div>
								</div>
								<div class="row setup-content" id="step-2">
									<div class="col-xs-12 col-center">
										<div class="col-md-12">
											<h3> Inclusión de Facturas</h3>
											<div class="form-group">
												<label class="control-label">Ingrese Número de Factura a Despachar</label>
												<input maxlength="10" type="text" required="required" class="form-control" placeholder="Numero de Factura" />
											</div>
									<!-- <div class="form-group">
										<label class="control-label">Company Address</label>
										<input maxlength="200" type="text" required="required" class="form-control" placeholder="Enter Company Address"  />
									</div> -->
									<button class="btn btn-primary" type="button pull-left" >Añadir</button>
									<button class="btn btn-success float-right" type="button" >Siguiente</button>
								</div>
							</div>
						</div>
						<div class="row setup-content" id="step-3">
							<div class="col-xs-12 col-center">
								<div class="col-md-12">
									<h3> Step 3</h3>
									<button class="btn btn-success btn-sm float-right" type="submit">Finish!</button>
								</div>
							</div>
						</div>
					</form>
				</div>
				<!-- BOX  LOADER -->
				<!-- <figure id="loader">
					<div class="dot white"></div>
					<div class="dot"></div>
					<div class="dot"></div>
					<div class="dot"></div>
					<div class="dot"></div>
				</figure> -->
			</div>
			<div class="card card-info" id="tabla">
				<div class="card-header">
					<h3 class="card-title">Relación de Facturas a Despachar</h3>
				</div>
				<div class="card-body" style="width:auto;">
					<table class="table table-hover table-condensed table-bordered table-striped" style="width:100%;" id="despacho_data">
						<thead style="background-color: #17A2B8;color: white;">
							<tr>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Factura">Factura</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Fecha">Fecha</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Razón Social">Razón Social</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Zona">Zona</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Ruta O EDV">Ruta</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Monto">Monto</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Peso (Kg)">Peso (Kg)</th>
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
								<th style="text-align: center;">Acción</th>
							</tr>
						</tfoot>
						<tbody>
							<!-- TD TABLA LLEGAN POR AJAX -->
						</tbody>
					</table>
					<!-- BOX BOTONES DE REPORTES-->
					<!-- <div align="center">
						<button type="button" class="btn btn-info" id="btn_excel">Exportar a Excel</button>
						<button type="button" class="btn btn-info" id="btn_pdf">Exportar a PDF</button>
					</div> -->
				</div>
			</section>
		</div>
		<?php require_once("../footer.php");?>
		<script type="text/javascript" src="despachos.js"></script>
	</body>
	</html>
