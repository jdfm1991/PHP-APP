<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
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
                            <h2>Kpi Manager's</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                                <li class="breadcrumb-item active">Kpi Manager's</li>
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
                        <h3 class="card-title">Rutas Registradas</h3><!-- overflow:scroll; -->
                    </div>
                    <div class="card-body" style="width:auto;">
                        <table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="tabla">
                            <thead style="background-color: #17A2B8;color: white;">
                            <tr>
                                <td class="text-center" title="Ruta">Ruta</td>
                                <td class="text-center" title="Nombre">Nombre</td>
                                <td class="text-center" title="Clase">Clase</td>
                                <td class="text-center" title="Depósito">Ubicación</td>
                                <td class="text-center" title="Acción">Acciónes</td>
                            </tr>
                            </thead>
                            <tfoot style="background-color: #ccc;color: white;">
                            <tr>
                                <td class="text-center">Ruta</td>
                                <td class="text-center">Nombre</td>
                                <td class="text-center">Clase</td>
                                <td class="text-center">Depósito</td>
                                <td class="text-center">Acciónes</td>
                            </tr>
                            </tfoot>
                            <tbody>
                            <!-- TD de la tabla que se pasa por ajax -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>

            <!-- MODAL CREAR O EDITAR USUARIO -->
            <?php include 'modales/editar_ruta.html' ?>
        </div>
        <?php require_once("../footer.php"); ?>

        <script type="text/javascript" src="kpimanager.js"></script><?php
    }
    ?>
</body>
</html>
