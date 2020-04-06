<?php
require_once("../acceso/conexion.php");
require_once("../acceso/const.php");
require_once("Usuarios_modelo.php");
$usuarios = new Usuarios();
$cat = $usuarios->get_roles();
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
						<button class="btn btn-primary" id="add_button" onclick="limpiar()" data-toggle="modal" data-target="#usuarioModal"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Usuario</button>
						<hr>
						<div class="card card-info">
							<div class="card-header">
								<h3 class="card-title">Usuarios Registrados</h3><!-- overflow:scroll; -->
							</div>
							<div class="card-body" style="width:auto;">
								<table class="table table-hover table-condensed table-bordered table-striped" style="width:100%;" id="usuario_data">
									<thead style="background-color: #17A2B8;color: white;">
										<tr>
											<td class="text-center" data-toggle="tooltip" data-placement="top" title="# Cedula"># Cedula</td>
											<td class="text-center" data-toggle="tooltip" data-placement="top" title="Login">Login</td>
											<td class="text-center" data-toggle="tooltip" data-placement="top" title="Nombre y Apellido">Nombre y Apellido</td>
											<td class="text-center" data-toggle="tooltip" data-placement="top" title="Correo Electronico">Correo Electrónico</td>
											<td class="text-center" data-toggle="tooltip" data-placement="top" title="Rol">Rol</td>
											<td class="text-center" data-toggle="tooltip" data-placement="top" title="Fecha de Registro">Fecha de Registro</td>
											<td class="text-center" data-toggle="tooltip" data-placement="top" title="Fecha Ultimo Ingreso">Fecha Ultimo Ingreso</td>
											<td class="text-center" data-toggle="tooltip" data-placement="top" title="Acción">Acciónes</td>
										</tr>
									</thead>
									<tfoot style="background-color: #ccc;color: white;">
										<tr>
											<td class="text-center"># Cedula</td>
											<td class="text-center">Login</td>
											<td class="text-center">Nombre y Apellido</td>
											<td class="text-center">Correo Electrónico</td>
											<td class="text-center">Rol</td>
											<td class="text-center">Fecha de Registro</td>
											<td class="text-center">Fecha Ultimo Ingreso</td>
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
		<div class="modal fade"  id="usuarioModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Agregar Usuario</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form method="post" id="usuario_form">
							<label>Cedula de Identidad</label>
							<input type="text" class="form-control input-sm" maxlength="20" id="cedula" name="cedula" placeholder="Ingrese Cedula de Identidad" required >
							<br />
							<label>Nombre de Usuario</label>
							<input type="text" class="form-control input-sm" maxlength="30" id="login" name="login" placeholder="Ingrese nombre de usuario" >
							<br />
							<label>Nombre y Apellido</label>
							<input type="text" class="form-control input-sm" maxlength="100" id="nomper" name="nomper" placeholder="Ingrese nombre y apellido">
							<br />
							<label>Correo Electrónico</label>
							<input type="email" class="form-control input-sm" maxlength="100" id="email" name="email" placeholder="Ingrese correo electronico">
							<br />
							<label>Contraseña</label>
							<input type="password" class="form-control input-sm" minlength="6" maxlength="20" id="clave" name="clave" placeholder="Ingrese contraseña">
							<br />
							<label>Rol de Usuario</label>
							<select class="form-control custom-select" name="rol" id="rol" style="width: 100%;" required>
								<option value="">Seleccione un rol de usuario</option>
								<?php
								foreach ($cat as $query) {
									echo '<option value="' . $query['ID'] . '">'. substr($query['Descripcion'], 0, 35). '</option>';}?>
								</select>
								<br />
								<label>Estado</label>
								<select class="form-control custom-select" id="estado" name="estado">
									<option value="">Seleccione un Estado</option>
									<option value="0">Inactivo</option>
									<option value="1">Activo</option>
								</select>
								<br />
								<div class="modal-footer">
									<input type="hidden" name="id_usuario" id="id_usuario"/>
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
		<script type="text/javascript" src="usuarios.js"></script>
	</body>
	</html>
