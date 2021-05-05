<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");
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
						<h2>Clientes COD Nestle</h2>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
							<li class="breadcrumb-item active">Clientes COD Nestle</li>
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
                            <div class="col-sm-2">
                                <div class="custom-control custom-radio pt-2">
                                    <input class="custom-control-input" type="radio" name="opc" id="todos" value="1">
                                    <label class="custom-control-label" for="todos">Ver Todos</label>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="custom-control custom-radio pt-2">
                                    <input class="custom-control-input" type="radio" name="opc" id="concode" value="2">
                                    <label class="custom-control-label" for="concode">Con Código Nestle</label>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="custom-control custom-radio pt-2">
                                    <input class="custom-control-input" type="radio" name="opc" id="sincode" value="3">
                                    <label class="custom-control-label" for="sincode">Sin Código Nestle</label>
                                </div>
                            </div>
							<div class="col-sm-3">
                                <select class="form-control custom-select" name="vendedor" id="vendedor" style="width: 100%;" required>
                                    <!-- lista de vendedores se carga por ajax -->
                                </select>
                            </div>
                        </div>
						</form>
					</div>
					<!-- BOX BOTON DE PROCESO -->
					<div class="card-footer">
						<button type="submit" class="btn btn-success" id="btn_clientescodnestle"><i class="fa fa-search" aria-hidden="true"></i> Consultar</button>
					</div>
				</div>

				<!-- BOX TABLA -->
				<div class="card card-info" id="tabla">
					<div class="card-header">
						<h3 class="card-title">Relación de Clientes COD Nestle</h3>
					</div>
					<div class="card-body" style="width:auto;">
						<table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="clientescodnestle_data">
							<thead style="background-color: #17A2B8;color: white;">
								<tr>
									<th class="text-center" title="Ruta">Ruta</th>
									<th class="text-center" title="Codigo del Cliente">Codigo Cliente</th>
									<th class="text-left" title="Razón Social">Razón Social</th>
									<th class="text-center" title="Rif">Rif</th>
									<th class="text-center" title="Fecha de Apertura">Fecha de Apertura</th>
									<th class="text-center" title="Dia de Visita">Dia de Visita</th>
									<th class="text-center" title="Codificación Nestle">Codigo Nestle</th>
								</tr>
							</thead>
							<tfoot style="background-color: #ccc;color: white;">
								<tr>
									<th class="text-center">Ruta</th>
									<th class="text-center">Codigo Cliente</th>
									<th class="text-center">Razón Social</th>
									<th class="text-center">Rif</th>
									<th class="text-center">Fecha de Apertura</th>
									<th class="text-center">Dia de Visita</th>
									<th class="text-center">Codigo Nestle</th>
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
			<script type="text/javascript" src="clientescodnestle.js"></script>
		</body>
		</html>
