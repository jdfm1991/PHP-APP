<?php
//LLAMAMOS A LA CONEXION.
//LLAMAMOS A LAS CONSTANTES.
require_once("../acceso/conexion.php");
require_once("../acceso/const.php");
require_once("../costodeinventario/costodeinventario_modelo.php");
$almacen = new CostodeInventario();
$almacenes = $almacen->get_Almacenes();
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
						<h2>Inventario Global [A LA ESPERA QUE EL MODULO DE DESPACHOS ESTE LISTO]</h2>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
							<li class="breadcrumb-item active">Inventario Global</li>
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
					<h3 class="card-title">Seleccione los Almacenes</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fas fa-minus"></i></button>
						<button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove"><i class="fas fa-times"></i></button>
					</div>
				</div>
				<!-- BOX CARD QUE CONTIENE EL FORMULARIO QUE SE CIERRA -->
				<div class="card-body" id="minimizar">
					<form id="frminventario" class="form-horizontal">
						<div class="form-group col-sm-4 select2-blue">
							<label>Almacen</label>
							<select class="select2" name="depo[]" id="depo[]" multiple="multiple" data-placeholder="Seleccione Almacén" data-dropdown-css-class="select2-blue" style="width: 100%;" required>
								<?php
								foreach ($almacenes as $query) {
									echo '<option value="' . $query['codubi'] . '">' . $query['codubi'] . ': ' . substr($query['descrip'], 0, 35) . '</option>';
								} ?>
							</select>
						</div>
					</form>
				</div>
				<!-- BOX BOTON DE PROCESO -->
				<div class="card-footer">
					<button type="submit" class="btn btn-success" id="btn_inventarioglobal"><i class="fa fa-search" aria-hidden="true"></i> Consultar</button>
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
					<h3 class="card-title">Inventario Global</h3>
				</div>
				<div class="card-body" style="width:auto;">
					<table class="table table-hover table-condensed table-bordered table-striped" style="width:100%;" id="inventarioglobal_data">
						<thead style="background-color: #17A2B8;color: white;">
							<tr>
								<th class="text-center" data-toggle="tooltip" data-placement="top" title="Codigo">Codigo</th>
								<th class="text-center" data-toggle="tooltip" data-placement="top" title="Producto">Producto</th>
								<th class="text-center" data-toggle="tooltip" data-placement="top" title="Cantidad Bultos Por Despachar">Cantidad Bultos por Despachar</th>
								<th class="text-center" data-toggle="tooltip" data-placement="top" title="Cantidad Paquetes por Despachar">Cantidad Paquetes por Despachar</th>
								<th class="text-center" data-toggle="tooltip" data-placement="top" title="Cantidad Bultos Sistema">Cantidad Bultos Sistema</th>
								<th class="text-center" data-toggle="tooltip" data-placement="top" title="Canidad Paquetes Sistema">Canidad Paquetes Sistema</th>
								<th class="text-center" data-toggle="tooltip" data-placement="top" title="Total Inventario Bultos">Total Inventario Bultos</th>
								<th class="text-center" data-toggle="tooltip" data-placement="top" title="Total Inventario Paquetes">Total Inventario Paquetes</th>
							</tr>
						</thead>
						<tfoot style="background-color: #ccc;color: white;">
							<tr>
								<th class="text-center">Codigo</th>
								<th class="text-center">Producto</th>
								<th class="text-center">Cantidad Bultos por Despachar</th>
								<th class="text-center">Cantidad Paquetes por Despachar</th>
								<th class="text-center">Cantidad Bultos Sistema</th>
								<th class="text-center">Cantidad Paquetes Sistema</th>
								<th class="text-center">Total Inventario Bultos</th>
								<th class="text-center">Total Inventario Paquetes</th>
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
						<button type="button" class="btn btn-info" id="btn_excel">Exportar a Excel</button>
						<button type="button" class="btn btn-info" id="btn_pdf">Exportar a PDF</button>
					</div>
				</div>
			</div>
		</section>
	</div>
	<?php require_once("../footer.php"); ?>
	<script type="text/javascript" src="inventarioglobal.js"></script>
</body>

</html>