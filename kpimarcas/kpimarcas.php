<?php
//LLAMAMOS A LA CONEXION.
//LLAMAMOS A LAS CONSTANTES.
require_once("../acceso/conexion.php");
require_once("../acceso/const.php");
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
                    <h2>Kpi Marcas</h2>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Kpi marcas</li>
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
                <h3 class="card-title">Seleccione las Marcas</h3>
            </div>
            <!-- BOX CARD QUE CONTIENE EL FORMULARIO QUE SE CIERRA -->
            <div class="card-body" id="minimizar">
                <form id="frm_kpimarcas" class="form-horizontal">
                    <div class="row">
                        <dt class="col-6">TODAS LAS MARCAS</dt>
                        <dt class="col-6">MARCAS VISIBLES PARA EL KPI</dt>
                    </div>
                    <div class="row">
                        <div class="form-group col-12">
                            <select name="marcas[]" id="marcas[]" class="duallistbox" multiple="multiple">
                                <!-- la lista de marcas se carga por ajax -->
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <!-- BOX BOTON DE PROCESO -->
            <div class="card-footer">
                <button type="submit" class="btn btn-success" id="btn_guardar">
                    <i class="fa fa-check" aria-hidden="true"></i> Guardar
                    <span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>

</div>
</section>
</div>
<?php require_once("../footer.php"); ?>
<!-- Bootstrap4 Duallistbox -->
<script src="<?php echo SERVERURL; ?>public/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>

<script type="text/javascript" src="kpimarcas.js"></script>
</body>
</html>
