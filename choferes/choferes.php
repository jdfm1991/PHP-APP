<?php
require_once("../acceso/conexion.php");
require_once("../acceso/const.php");
?>
<!DOCTYPE html>
<html>
<!-- head -->
<?php require_once("../header.php");?>
<body class="hold-transition sidebar-mini layout-fixed">
	<?php require_once("../menu_lateral.php");?>
	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<div class="row">
				<div class="col-sm-12">
					<div class="card-body">
						<button class="btn btn-primary" id="add_button" onclick="limpiar()" data-toggle="modal" data-target="#choferModal"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Chofer</button>
						<hr>
						<div class="card card-info">
							<div class="card-header">
								<h3 class="card-title">Choferes Registrados</h3><!-- overflow:scroll; -->
							</div>
							<div class="card-body" style="width:auto;">
								<table class="table table-hover table-condensed table-bordered table-striped" style="width:100%;" id="choferes_data">
									<thead style="background-color: #17A2B8;color: white;">
										<tr>
											<td class="text-center" data-toggle="tooltip" data-placement="top" title="# Cedula"># Cedula</td>
											<td class="text-center" data-toggle="tooltip" data-placement="top" title="Nombre y Apellido">Nombre y Apellido</td>
											<td class="text-center" data-toggle="tooltip" data-placement="top" title="Fecha de Registro">Fecha de Registro</td>
											<td class="text-center" data-toggle="tooltip" data-placement="top" title="Acción">Acciónes</td>
										</tr>
									</thead>
									<tfoot style="background-color: #ccc;color: white;">
										<tr>
											<td class="text-center"># Cedula</td>
											<td class="text-center">Nombre y Apellido</td>
											<td class="text-center">Fecha de Registro</td>
											<td class="text-center">Acciónes</td>
										</tr>
									</tfoot>
									<tbody>
										<!-- TD de la tabla que se pasa por ajax -->
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- Modal crear -->
		<div class="modal fade"  id="choferModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Agregar Choferes</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form method="post" id="chofer_form">
							<label>Cedula de Identidad</label>
							<input type="text" class="form-control input-sm" maxlength="20" id="cedula" name="cedula" placeholder="Ingrese Cedula de Identidad" required >
							<br />
							<label>Nombre y Apellido</label>
							<input type="text" class="form-control input-sm" maxlength="100" id="nomper" name="nomper" placeholder="Ingrese nombre y apellido">
							<br />
							<label>Estado</label>
							<select class="form-control select2" id="estado" name="estado">
								<option value="">Seleccione un Estado</option>
								<option value="0">Inactivo</option>
								<option value="1">Activo</option>
							</select>
							<br />
							<div class="modal-footer">
								<input type="hidden" name="id_chofer" id="id_chofer"/>
								<button type="submit" name="action" id="btnGuardar" class="btn btn-success pull-left" value="Add">Guardar</button>
								<button type="button" onclick="limpiar()" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Cerrar</button>
							</div>
						</form>
					</div>

				</div>
			</div>
		</div>
	</div>
	<!-- /.content-wrapper -->
	<?php require_once("../footer.php");?>
	<script type="text/javascript" src="choferes.js"></script>
</body>
</html>
