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
                    <h2 id="title_permisos">Permisos</h2>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <input id="btnGestion" type="button" class="btn btn-outline-primary mr-3" value="Gestión permisos" />
                        <input id="btnVolver"  type="button" class="btn btn-outline-secondary" value="Volver a roles" />
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <!-- BOX DEL CONTENIDO DE LA VISTA FORMULARIO Y TABLA -->
    <section class="content">

        <!-- BOX TABLA -->
        <div class="card card-info" id="tabla">
            <div class="card-header">
                <h3 class="card-title">Permisos</h3>
            </div>
            <div class="card-body" style="width:auto;">
                <form id="permisos_form">
                    <h3 class="card-title text-center">Seleccione los permisos para el rol seleccionado</h3>

                    <div id="permisos" class="mt-4">
                        <!--se cargan por ajax-->
                    </div>

                    <div class="text-left m-t-10">
                        <input type="hidden" name="tipo" id="tipo" value="<?php echo $_GET['t'] ?>"/>
                        <input type="hidden" name="id" id="id" value="<?php echo $_GET['i'] ?>"/>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<?php require_once("../footer.php");?>
<script type="text/javascript" src="permisos.js"></script>
</body>
</html>
