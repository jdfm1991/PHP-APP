<?php
//LLAMAMOS A LA CONEXION.
//LLAMAMOS A LAS CONSTANTES.
require_once("../acceso/conexion.php");
require_once("../acceso/const.php");
require_once("../listadeprecio/listadeprecio_modelo.php");
require_once("../sellin/sellin_modelo.php");
$almacenes = new Listadeprecio();
$marcas = new sellin();
$cat = $almacenes->get_Almacenes();
$marcas = $marcas->get_marcas();
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
						<h2>Lista de Precio e Inventario</h2>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
							<li class="breadcrumb-item active">Lista de Precio e Inventario</li>
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
					<h3 class="card-title">Seleccione las Siguientes Opciones</h3>
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
								<div class="form-check form-check-inline">
									<select class="custom-select" name="depo" id="depo"  required>
										<option value="">Seleccione Almacen</option>
										<?php
										foreach ($cat as $query) {
											echo '<option value="' . $query['codubi'] . '">' . $query['codubi'] . ': ' . substr($query['descrip'], 0, 35). '</option>';}?>
										</select>
									</div>
									<div class="form-check form-check-inline">
										<select class="custom-select" name="marca" id="marca" style="width: 100%;" required>
											<option value="">Seleccione una Marca</option>
											<option value="-">TODAS</option>
											<?php
											foreach ($marcas as $query) {
												echo '<option value="' . $query['marca'] . '">' . $query['marca'] . '</option>';}?>
											</select>
										</div>
										<div class="form-check form-check-inline">
											<select class="custom-select" name="orden" id="orden" style="width: 100%;" required>
												<option value="">Ordenar por: </option>
												<option value="codprod">Código</option>
												<option value="descrip">Descripción</option>
												<option value="marca">Marca</option>
											</select>
										</div>
									</div>
								</div>
								<br>
								<div class="form-group row">
									<div class="custom-control custom-checkbox col-sm-1">
										<input class="custom-control-input" type="checkbox" id="p1" value="checkbox"  name="checkbox">
										<label for="p1" class="custom-control-label">Precio 1</label>
									</div>
									<div class="custom-control custom-checkbox col-sm-1">
										<input class="custom-control-input" type="checkbox" id="p2" value="checkbox"  name="checkbox">
										<label for="p2" class="custom-control-label">Precio 2</label>
									</div>
									<div class="custom-control custom-checkbox col-sm-1">
										<input class="custom-control-input" type="checkbox" id="p3" value="checkbox"  name="checkbox">
										<label for="p3" class="custom-control-label">Precio 3</label>
									</div>
									<div class="custom-control custom-checkbox col-sm-1">
										<input class="custom-control-input" type="checkbox" id="iva" value="checkbox"  name="checkbox" checked="checked">
										<label for="iva" class="custom-control-label">IVA (16%)</label>
									</div>
									<div class="custom-control custom-checkbox col-sm-1">
										<input class="custom-control-input" type="checkbox" id="cubi" value="checkbox"  name="checkbox">
										<label for="cubi" class="custom-control-label">Cubicaje</label>
									</div>
									<div class="custom-control custom-checkbox col-sm-1">
										<input class="custom-control-input" type="checkbox" id="exis" value="checkbox"  name="checkbox" checked="checked">
										<label for="exis" class="custom-control-label">Con Existencia</label>
									</div>
								</div>
							</form>
						</div>
						<!-- BOX BOTON DE PROCESO -->
						<div class="card-footer">
							<button type="submit" class="btn btn-success" id="btn_listadeprecio"><i class="fa fa-search" aria-hidden="true"></i> Consultar</button>
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
							<h3 class="card-title">Lista de Precio e Inventario</h3>
						</div>
						<div class="card-body" style="width:auto;">
							<table class="table table-hover table-condensed table-bordered table-striped" style="width:100%;" id="listadeprecio_data">
								<thead style="background-color: #17A2B8;color: white;">
									<tr>
										<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Ruta">Codigo</th>
										<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Codigo del Cliente">Decripción del Producto</th>
										<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Razón Social">Marca</th>
										<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Rif">Cantidad Bultos</th>
										<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Fecha de Apertura">Precio 1 Bulto</th>
										<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Dia de Visita">Precio 2 Bulto</th>
										<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Codificación Nestle">Precio 3 Bulto</th>
										<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Rif">Cantidad Paquetes</th>
										<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Fecha de Apertura">Precio 1 Paquete</th>
										<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Dia de Visita">Precio 2 Paquete</th>
										<th style="text-align: center;" data-toggle="tooltip" data-placement="top" title="Codificación Nestle">Precio 3 Paquete</th>

									</tr>
								</thead>
								<tfoot style="background-color: #ccc;color: white;">
									<tr>
										<th style="text-align: center;">Codigo</th>
										<th style="text-align: center;">Decripción del Producto</th>
										<th style="text-align: center;">Marca</th>
										<th style="text-align: center;">Cantidad Bultos</th>
										<th style="text-align: center;">Precio 1 Bulto</th>
										<th style="text-align: center;">Precio 2 Bulto</th>
										<th style="text-align: center;">Precio 3 Bulto</th>
										<th style="text-align: center;">Cantidad Paquetes</th>
										<th style="text-align: center;">Precio 1 Paquete</th>
										<th style="text-align: center;">Precio 2 Paquete</th>
										<th style="text-align: center;">Precio 3 Paquete</th>
									</tr>
								</tfoot>
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
					</section>
				</div>
				<?php require_once("../footer.php");?>
				<script type="text/javascript" src="listadeprecio.js"></script>
			</body>
			</html>
