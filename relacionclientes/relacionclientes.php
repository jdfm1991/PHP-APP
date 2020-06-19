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
                    <button class="btn btn-primary" id="add_button" onclick="limpiar()" data-toggle="modal" data-target="#clienteNuevoModal"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Cliente</button>
                    <hr>
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Relación de Clientes</h3><!-- overflow:scroll; -->
                        </div>
                        <div class="card-body" style="width:auto;">
                            <table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="cliente_data">
                                <thead style="background-color: #17A2B8;color: white;">
                                <tr>
                                    <th data-toggle="tooltip" data-placement="top" title="Codigo Cliente">Codigo Cliente</th>
                                    <th data-toggle="tooltip" data-placement="top" title="Razón Social">Razón Social</th>
                                    <th data-toggle="tooltip" data-placement="top" title="Rif">Rif</th>
                                    <th data-toggle="tooltip" data-placement="top" title="Saldo">Saldo</th>
                                    <th data-toggle="tooltip" data-placement="top" title="Acción">Acción</th>
                                </tr>
                                </thead>
                                <tfoot style="background-color: #ccc;color: white;">
                                <tr>
                                    <th style="text-align: center;">Codigo Cliente</th>
                                    <th style="text-align: center;">Razón Social</th>
                                    <th style="text-align: center;">Rif</th>
                                    <th style="text-align: center;">Saldo</th>
                                    <th style="text-align: center;">Acción</th>
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
    <div class="modal fade"  id="clienteNuevoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Cliente</h5>
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
/*                            foreach ($cat as $query) {
                                echo '<option value="' . $query['ID'] . '">'. substr($query['Descripcion'], 0, 35). '</option>';}*/?>
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
<script type="text/javascript" src="relacionclientes.js"></script>
</body>
</html>
