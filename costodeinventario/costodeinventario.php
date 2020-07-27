<?php
//LLAMAMOS A LA CONEXION.
//LLAMAMOS A LAS CONSTANTES.
require_once("../acceso/conexion.php");
require_once("../acceso/const.php");
require_once("../sellin/sellin_modelo.php");
require_once("costodeinventario_modelo.php");
$marcas = new sellin();
$costo = new CostodeInventario();
$marca = $marcas->get_marcas();
$almacenes = $costo->get_Almacenes();
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
						<h2>Costos de Inventario</h2>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
							<li class="breadcrumb-item active">Costos de Inventario</li>
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
					<form class="form-horizontal" id="frmCostos" >
						<div class="form-group row">
							<div class="form-group col-sm-3">
								<label>Marca</label>
								<select class="custom-select" name="marca" id="marca" style="width: 100%;" required>
									<option value="">Seleccione una Marca</option>
									<option value="-">TODAS</option>
									<?php
									foreach ($marca as $query) {
										echo '<option value="' . $query['marca'] . '">' . $query['marca'] . '</option>';
									}?>
								</select>
							</div>
							<div class="form-group col-sm-4 select2-blue">
								<label>Almacen</label>
								<select class="select2" name="depo[]" id="depo[]" multiple="multiple" data-placeholder="Seleccione Deposito" data-dropdown-css-class="select2-blue" style="width: 100%;" required>
									<?php
									foreach ($almacenes as $query) {
										echo '<option value="' . $query['codubi'] . '">' . $query['codubi'] . ': ' . substr($query['descrip'], 0, 35) . '</option>';
									}?>
								</select>
							</div>

						</div>
					</form>
				</div>
				<!-- BOX BOTON DE PROCESO -->
				<div class="card-footer">
					<button type="submit" class="btn btn-success" id="btn_costodeinventario"><i class="fa fa-search" aria-hidden="true"></i> Consultar</button>
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
                    <h3 class="card-title">Costos de Inventario</h3>
                </div>
                <div class="card-body table-responsive p-0" style="width:100%; height:400px;">
                    <table class="table table-hover table-condensed table-bordered table-striped table-head-fixed text-nowrap">
                        <thead style="color: black;">
                        <tr>
                            <th class="text-center">Codigo</th>
                            <th class="text-center">Producto</th>
                            <th class="text-center">Marca</th>
                            <th class="text-center">Costo Bultos</th>
                            <th class="text-center">Costo Unidad</th>
                            <th class="text-center">Precio</th>
                            <th class="text-center">Bultos</th>
                            <th class="text-center">Paquetes</th>
                            <th class="text-center">Total Costo Bultos</th>
                            <th class="text-center">Total Costo Unidades</th>
                            <th class="text-center">Peso</th>
                        </tr>
                        </thead>
                        <tbody>
                            <!-- TD TABLA LLEGAN POR AJAX -->
                        </tbody>
                    </table>
                </div>
                <br>
                <div align="center">
                    <br>
                    <br><p>Total de Item:<code><?php echo "  $num  "; ?></code></p><br>
                    <button type="button" class="btn btn-info" id="btn_excel">Exportar a Excel</button>
                    <button type="button" class="btn btn-info" id="btn_pdf">Exportar a PDF</button>
                    <br>
                    <br>
                </div>
            </div>

            <!--<div class="card card-info" id="costos_inv_ver"></div>-->
		</section>

	</div>
	<?php require_once("../footer.php");?>
	<script type="text/javascript" src="costodeinventario.js"></script>
</body>
</html>

