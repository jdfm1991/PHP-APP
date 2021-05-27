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
                    <h2 id="title_permisos">Gestión del Sistema</h2>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Gestión del Sistema</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- BOX DEL CONTENIDO DE LA VISTA FORMULARIO Y TABLA -->
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card card-info card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                            <li class="pt-2 px-3"><h3 class="card-title">Gestión</h3></li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tab-modulos" data-toggle="pill" href="#tab-modulos" role="tab" aria-controls="tab-modulos" aria-selected="true">
                                    Módulos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tab-menu" data-toggle="pill" href="#tab-menu" role="tab" aria-controls="tab-menu" aria-selected="false">
                                    Menús
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tab-notificaciones" data-toggle="pill" href="#tab-notificaciones" role="tab" aria-controls="tab-notificaciones" aria-selected="false">
                                    Notificaciones
                                </a>
                            </li>

                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-one-tabContent">
                            <div class="tab-pane fade show active" id="tab-modulos" role="tabpanel" aria-labelledby="custom-tab-modulos">
                                <div class="row">
                                    <div class="col-10 text-gray">
                                        se gestiona todos los módulos del sistema dependiendo de un menu
                                    </div>
                                    <div class="col-2 text-right">
                                        <button class="btn btn-primary" id="add_modulo_button" onclick="mostrar_modulo()" data-toggle="modal" data-target="#moduloModal"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Módulo</button>
                                    </div>
                                </div>
                                <div class="row mt-5">
                                    <div class="col-12">
                                        <table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="modulo_data">
                                            <thead style="background-color: #17A2B8;color: white;">
                                            <tr>
                                                <td class="text-center" title="Ruta">Ruta</td>
                                                <td class="text-center" title="Menú">Menú</td>
                                                <td class="text-center" title="Nombre">Nombre</td>
                                                <td class="text-center" title="Descripción">Descripción</td>
                                                <td class="text-center" title="Acción">Acciónes</td>
                                            </tr>
                                            </thead>
                                            <tfoot style="background-color: #ccc;color: white;">
                                            <tr>
                                                <td class="text-center">Ruta</td>
                                                <td class="text-center">Menú</td>
                                                <td class="text-center">Nombre</td>
                                                <td class="text-center">Descripción</td>
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
                            <div class="tab-pane fade" id="tab-menu" role="tabpanel" aria-labelledby="custom-tab-menu">
                                <div class="row">
                                    <div class="col-10 text-gray">
                                        se gestiona todos los Menús del sistema
                                    </div>
                                    <div class="col-2 text-right">
                                        <button class="btn btn-primary" id="add_modulo_button" onclick="mostrar_menu()" data-toggle="modal" data-target="#menuModal"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Menú</button>
                                    </div>
                                </div>
                                <div class="row mt-5 mb-5">
                                    <div class="col-12">
                                        <table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="menu_data">
                                            <thead style="background-color: #17A2B8;color: white;">
                                            <tr>
                                                <td class="text-center" title="Nombre menú">Nombre menú</td>
                                                <td class="text-center" title="Icono">Icono</td>
                                                <td class="text-center" title="Menú padre">Menú padre</td>
                                                <td class="text-center" title="Menú hijo">Menú hijo</td>
                                                <td class="text-center" title="orden">orden</td>
                                                <td class="text-center" title="Acción">Acciónes</td>
                                            </tr>
                                            </thead>
                                            <tfoot style="background-color: #ccc;color: white;">
                                            <tr>
                                                <td class="text-center">Nombre menú</td>
                                                <td class="text-center">Icono</td>
                                                <td class="text-center">Menú padre</td>
                                                <td class="text-center">Menú hijo</td>
                                                <td class="text-center">orden</td>
                                                <td class="text-center">Acciónes</td>
                                            </tr>
                                            </tfoot>
                                            <tbody>
                                            <!-- TD de la tabla que se pasa por ajax -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div id="chart-container"></div>

                            </div>
                            <div class="tab-pane fade" id="tab-notificaciones" role="tabpanel" aria-labelledby="custom-tab-notificaciones">
                                Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut ligula tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod pellentesque diam.
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>

    </section>

    <!-- MODAL CREAR O EDITAR MODULO -->
    <?php include 'modales/crear_o_editar_modulo.html' ?>

    <!-- MODAL CREAR O EDITAR MODULO -->
    <?php include 'modales/crear_o_editar_menu.html' ?>

</div>
<?php require_once("../footer.php");?>
<script src="<?php echo URL_HELPERS_JS; ?>Icons.js" type="text/javascript"></script>

<script type="text/javascript" src="gestionsistema.js"></script>
</body>
</html>
