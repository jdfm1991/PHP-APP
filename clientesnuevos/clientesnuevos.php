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
						<h2>Clientes Nuevos</h2>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
							<li class="breadcrumb-item active">Clientes Nuevos</li>
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
					<h3 class="card-title">Seleccione Rango de Fechas</h3>
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
								<!-- radio -->
								<div class="form-check form-check-inline">
									<label for="vutil" class="col-form-label col-sm-4">Inicial</label>
									<input type="date" class="form-control col-sm-9"  id="fechai" name="fechai" required>
								</div>&nbsp;&nbsp;&nbsp;
								<div class="form-check form-check-inline">
									<label for="vutil" class="col-form-label col-sm-4">Final</label>
									<input type="date" class="form-control col-sm-9"  id="fechaf" name="fechaf" required>
								</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
							</div>
						</div>
					</form>
				</div>
				<!-- BOX BOTON DE PROCESO -->
				<div class="card-footer">
					<button type="submit" class="btn btn-success" id="btn_clientesnuevos"><i class="fa fa-search" aria-hidden="true"></i> Consultar</button>
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
					<h3 class="card-title">Relación de Clientes Nuevos</h3>
				</div>
				<div class="card-body" style="width:auto;">
					<table class="table table-hover table-condensed table-bordered table-striped" style="width:100%;" id="clientesnuevos_data">
						<thead style="background-color: #17A2B8;color: white;">
							<tr>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Codigo Cliente">Codigo Cliente</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Razón Social">Razón Social</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="RIF">Rif</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Cliente desde - Ruta">Fecha</th>
								<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Pertenece a la Ruta">Ruta</th>
							</tr>
						</thead>
						<tfoot style="background-color: #ccc;color: white;">
							<tr>
								<th style="text-align: center;">Codigo Cliente</th>
								<th style="text-align: center;">Razón Social</th>
								<th style="text-align: center;">Rif</th>
								<th style="text-align: center;">Fecha</th>
								<th style="text-align: center;">Ruta</th>
							</tr>
						</tfoot>
						<tbody>
							<!-- TD TABLA LLEGAN POR AJAX -->
						</tbody>
					</table>
					<div align="center">
						<br>
						<p id="cuenta"></p>
						<br>
					</div>
					<!-- BOX BOTONES DE REPORTES-->
					<div align="center">
						<button type="button" class="btn btn-info" id="btn_excel">Exportar a Excel</button>
						<button type="button" class="btn btn-info" id="btn_pdf">Exportar a PDF</button>
					</div>
				</div>
			</section>
		</div>
		<?php require_once("../footer.php");?>
		<script type="text/javascript" src="clientesnuevos.js"></script>
	</body>
	</html>
