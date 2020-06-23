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
                    <button class="btn btn-primary" id="add_button" onclick="mostrar()" data-toggle="modal" data-target="#clienteModal"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Cliente</button>
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
    <div class="modal fade"  id="clienteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- seleccionamos el activo a registrar -->
                    <label>Seleccione el tipo de cliente</label>
                    <select class="form-control custom-select" id="tipoid3" name="tipoid3" style="width: 100%;" required>
                        <option value="">Seleccione</option>
                        <option value="0">Jurídico</option>
                        <option value="1">Natural</option>
                    </select>
                    <form method="post" id="cliente_juridico_form">
                        <br><hr />
                        <h2 class="card-title">Datos Principales</h2> <br><br>

                        <label>Codigo del Cliente *</label>
                        <input type="text" class="form-control input-sm" minlength="5" maxlength="15" id="codclie" name="codclie" placeholder="indique el RIF Ejemplo J311768773" required >
                        <br />
                        <label>Razón Social *</label>
                        <input type="text" class="form-control input-sm" minlength="3" maxlength="60" id="descrip" name="descrip" placeholder="razón social" required>
                        <br />
                        <label>RIF *</label>
                        <input type="text" class="form-control input-sm" minlength="5" maxlength="15" id="id3" name="id3" placeholder="RIF Ejemplo J311768773" required>
                        <br />
                        <label>Clase *</label>
                        <input type="text" class="form-control input-sm" minlength="2" maxlength="10" id="clase" name="clase" placeholder="tipo de canal" required>
                        <br />
                        <label>Representante *</label>
                        <input type="text" class="form-control input-sm" minlength="3" maxlength="40" id="represent" name="represent" placeholder="representante" required>
                        <br />
                        <label>Dirección 1 *</label>
                        <input type="text" class="form-control input-sm" minlength="3" maxlength="60" id="direc1" name="direc1" placeholder="dirección 1" required>
                        <br />
                        <label>Dirección 2</label>
                        <input type="text" class="form-control input-sm" minlength="3" maxlength="60" id="direc2" name="direc2" placeholder="dirección 2">
                        <br />
                        <label>Estado</label>
                        <select class="form-control custom-select" id="estado" name="estado" required>
                            <!--los estados se llenan por ajax-->
                        </select>
                        <br /><br />
                        <label>Ciudad *</label>
                        <select class="form-control custom-select" id="ciudad" name="ciudad" style="width: 100%;" required>
                            <option value="">Seleccione</option>
                        </select><br /><br />
                        <label>Municipio</label>
                        <input type="text" class="form-control input-sm" minlength="3" maxlength="60" id="municipio" name="municipio" placeholder="municipio" required>
                        <br />
                        <label>Email</label>
                        <input type="text" class="form-control input-sm" minlength="3" maxlength="60" id="email" name="email" placeholder="correo electrónico" required>
                        <br />
                        <label>Teléfono *</label>
                        <input type="tel" class="form-control input-sm" minlength="5" maxlength="30" id="telef" name="telef" placeholder="teléfono fijo">
                        <br />
                        <label>Movil</label>
                        <input type="tel" class="form-control input-sm" minlength="5" maxlength="15" id="movil" name="movil" placeholder="teléfono movil">
                        <br />
                        <label>Estatus *</label>
                        <select class="form-control custom-select" id="activo" name="activo" style="width: 100%;" required>
                            <option value="">Seleccione</option>
                            <option value="0">Inactivo</option>
                            <option value="1">Activo</option>
                        </select>
                        <br /><br />
                        <hr />
                        <h2 class="card-title">Datos Adicionales</h2> <br><br>

                        <label>Zona</label>
                        <select class="form-control custom-select" id="codzona" name="codzona" style="width: 100%;">
                            <!--las zonas se cargan por ajax-->
                        </select>
                        <br /><br />
                        <label>Vendedor</label>
                        <select class="form-control custom-select" id="codvend" name="codvend" style="width: 100%;">
                            <!--los vendedores se cargan por ajax-->
                        </select>
                        <br /><br />
                        <label>Tipo Contribuyente *</label>
                        <select class="form-control custom-select" id="f" name="tipocli" style="width: 100%;" required>
                            <option value="">Seleccione</option>
                            <option value="0">Contribuyente</option>
                            <option value="1">No contribuyente</option>
                            <option value="2">Exportacion</option>
                            <option value="3">Interno no gravable</option>
                            <option value="4">Contribuyente especial</option>
                        </select>
                        <br /><br />
                        <label>Tipo Precio *</label>
                        <select class="form-control custom-select" id="tipopvp" name="tipopvp" style="width: 100%;" required>
                            <option value="">Seleccione</option>
                            <option value="1">Precio 1</option>
                            <option value="2">Precio 2</option>
                            <option value="3">Precio 3</option>
                        </select>
                        <br /><br />
                        <label>Dia de Visita</label>
                        <select class="form-control custom-select" id="diasvisita" name="diasvisita" style="width: 100%;">
                            <option value="">Seleccione</option>
                            <option value="lunes">LUNES</option>
                            <option value="martes">MARTES</option>
                            <option value="miercoles">MIERCOLES</option>
                            <option value="jueves">JUEVES</option>
                            <option value="viernes">VIERNES</option>
                        </select>
                        <br /><br />
                        <label>Ruc</label>
                        <input type="text" class="form-control input-sm" maxlength="40" id="ruc" name="ruc" placeholder="ruc">
                        <br />
                        <label>Latitud</label>
                        <input type="text" class="form-control input-sm" maxlength="20" id="latitud" name="latitud" placeholder="latitud">
                        <br />
                        <label>Longintud</label>
                        <input type="text" class="form-control input-sm" maxlength="20" id="longitud" name="longitud" placeholder="longitud">
                        <br />
                        <label>Codigo Nestle *</label>
                        <select class="form-control custom-select" id="codnestle" name="codnestle" style="width: 100%;" required>
                            <!--los codigo nestle se cargan por AJAX-->
                        </select>
                        <br /><br />
                        <hr />
                        <h2 class="card-title">Datos Financieros</h2> <br><br>

                        <label>Tiene Crédito</label>
                        <select class="form-control custom-select" id="escredito" name="escredito" style="width: 100%;">
                            <option value="">Seleccione</option>
                            <option value="0">NO</option>
                            <option value="1">SI</option>
                        </select>
                        <br /><br />
                        <label>Limite de Crédito</label>
                        <input type="text" class="form-control input-sm" value="0" maxlength="10" id="LimiteCred" name="LimiteCred" placeholder="limite de credito">
                        <br />
                        <label>Dias de Credito</label>
                        <input type="text" class="form-control input-sm" value="0" maxlength="3" id="diascred" name="diascred" placeholder="dias de credito">
                        <br />
                        <label>Tiene Tolerancia</label>
                        <select class="form-control custom-select" id="estoleran" name="estoleran" style="width: 100%;">
                            <option value="">Seleccione</option>
                            <option value="0">NO</option>
                            <option value="1">SI</option>
                        </select>
                        <br /><br />
                        <label>Dias de Tolerancia</label>
                        <input type="text" class="form-control input-sm" value="0" maxlength="3" id="diasTole" name="diasTole" placeholder="dias de tolerancia">
                        <br />
                        <label>Descuento %</label>
                        <input type="text" class="form-control input-sm" value="0" maxlength="3" id="descto" name="descto" placeholder="porcentaje de descuento">
                        <br />
                        <label>Observación</label>
                        <input type="text" class="form-control input-sm" maxlength="40" id="observa" name="observa" placeholder="observación">
                        <br />

                    </form>
                    <form method="post" id="cliente_natural_form">
                        <br><hr />
                        <h2 class="card-title">Datos Principales</h2> <br><br>

                        <label>Codigo del Cliente *</label>
                        <input type="text" class="form-control input-sm" minlength="5" maxlength="15" id="codclie" name="codclie" placeholder="indique el RIF Ejemplo J311768773" required >
                        <br />
                        <label>Razón Social *</label>
                        <input type="text" class="form-control input-sm" minlength="3" maxlength="60" id="descrip" name="descrip" placeholder="razón social" required>
                        <br />
                        <label>RIF *</label>
                        <input type="text" class="form-control input-sm" minlength="5" maxlength="15" id="id3" name="id3" placeholder="RIF Ejemplo J311768773" required>
                        <br />
                        <label>Estado</label>
                        <select class="form-control custom-select" id="estado" name="estado" required>
                            <!--los estados se llenan por ajax-->
                        </select>
                        <br /><br />
                    </form>

                    <div class="modal-footer">
                        <input type="hidden" name="id_usuario" id="id_usuario"/>
                        <button type="submit" name="action" id="btnGuardarUsuario" class="btn btn-success pull-left" value="Add">Guardar</button>
                        <button type="button" onclick="limpiar()" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Cerrar</button>
                    </div>
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
