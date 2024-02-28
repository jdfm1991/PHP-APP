<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR_DCONFISUR');
session_start();
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

if (!isset($_SESSION['cedula'])) {
    session_destroy(); Url::redirect(URL_APP);
}
?>
<!DOCTYPE html>
<html>
<?php require_once("../header.php"); ?>
<body class="hold-transition sidebar-mini layout-fixed">
    <?php require_once("../menu_lateral.php");
    if (!PermisosHelpers::verficarAcceso( Functions::getNameDirectory() )) {
        include ('../errorNoTienePermisos.php');
    }
    else { ?>
        <!-- BOX COMPLETO DE LA VISTA -->
        <div class="content-wrapper">
            <!-- BOX DE LA MIGA DE PAN -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h2>KPI Marcas (New)</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                                <li class="breadcrumb-item active">KPI</li>
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
                        <h3 class="card-title">Seleccione las Siguientes Opciones</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fas fa-minus"></i></button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <!-- BOX CARD QUE CONTIENE EL FORMULARIO QUE SE CIERRA -->
                    <div class="card-body" id="minimizar">
                        <form class="form-horizontal" id="kpi_form">
                            <div class="form-group row">
                                <div class="form-group col-sm-3">
                                    <label>Fecha de Consulta</label>
                                    <select class="form-control custom-select" name="fecha" id="fecha" style="width: 100%;" required>
                                    <option name="" value="01" selected="true">Enero</option>
                                    <option name="" value="02">Febrero</option>
                                    <option name="" value="03">Marzo</option>
                                    <option name="" value="04">Abril</option>
                                    <option name="" value="05">Mayo</option>
                                    <option name="" value="06">Junio</option>
                                    <option name="" value="07">Julio</option>
                                    <option name="" value="08">Agosto</option>
                                    <option name="" value="09">Septiembre</option>
                                    <option name="" value="10">Octubre</option>
                                    <option name="" value="11">Noviembre</option>
                                    <option name="" value="12">Diciembre</option>
                                    </select>
                                </div>
                            </div>
                           
                        </form>
                    </div>
                    <!-- BOX BOTON DE PROCESO -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success" id="btn_consultar">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <?=Strings::titleFromJson('boton_consultar')?>
                        </button>
                    </div>
                </div>

            </section>
        </div>

        <?php require_once("../footer.php"); ?>
        <script src="<?php echo URL_HELPERS_JS; ?>Number.js" type="text/javascript"></script>

        <script type="text/javascript" src="reporteKpiMarcas.js"></script><?php
    }
    ?>
</body>
</html>

