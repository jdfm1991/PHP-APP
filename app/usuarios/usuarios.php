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
						<button class="btn btn-primary" id="add_button" onclick="mostrar()" data-toggle="modal" data-target="#usuarioModal"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Usuario</button>
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

        <!-- MODAL CREAR O EDITAR USUARIO -->
        <?php include 'modales/crear_o_editar_usuario.html' ?>
    </div>
    <!-- /.content-wrapper -->
    <?php require_once("../footer.php");?>
    <script type="text/javascript" src="usuarios.js"></script>
</body>
</html>
