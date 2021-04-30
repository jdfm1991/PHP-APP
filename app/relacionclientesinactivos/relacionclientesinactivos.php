<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
require_once("../../config/conexion.php");
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
                    <div class="card card-info"  id="tabla">
                        <div class="card-header">
                            <h3 class="card-title">Relación de Clientes Inactivos</h3><!-- overflow:scroll; -->
                        </div>

                        <div class="card-body" style="width:auto;">
                            <table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="cliente_data">
                                <thead style="background-color: #17A2B8;color: white;">
                                <tr>
                                    <th class="text-center" title="Codigo Cliente">Codigo Cliente</th>
                                    <th class="text-center" title="Razón Social">Razón Social</th>
                                    <th class="text-center" title="Rif">Rif</th>
                                    <th class="text-center" title="Acción">Acción</th>
                                </tr>
                                </thead>
                                <tfoot style="background-color: #ccc;color: white;">
                                <tr>
                                    <th class="text-center">Codigo Cliente</th>
                                    <th class="text-center">Razón Social</th>
                                    <th class="text-center">Rif</th>
                                    <th class="text-center">Acción</th>
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

</div>
<!-- /.content-wrapper -->
<?php require_once("../footer.php");?>
<script type="text/javascript" src="relacionclientesinactivos.js"></script>
</body>
</html>
