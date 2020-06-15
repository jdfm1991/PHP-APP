

<!-- MODAL PRINCIPAL DE EDITAR DESPACHOS -->
<div class="modal fade"  id="editarDespachoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Despachos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- cabecera del despacho -->
                <div id="detalle_en_editar_despacho"></div>

                <!-- documentos del despacho -->
                <div class="card-body" style="width:auto;">
                    <table class="table table-bordered table-striped table-sm  text-center" style="width:100%;" id="tabla_editar_despacho">
                        <thead>
                            <tr>
                                <th>Nro Factura</th>
                                <th>Cod Cliente</th>
                                <th>Cliente</th>
                                <th>Fecha Emisión</th>
                                <th>Monto Bs</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <!-- BOX  LOADER -->
                <figure id="loader_editar_despacho">
                    <div class="dot white"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </figure>
                </br>
                <div class="modal-footer">
                    <div class="col-md-12">
                    <button class="btn btn-primary pull-left" id="buscarxfact_button"  onclick="" type="button">Agregar Factura</button>
                    <button type="button" class="btn btn-danger float-right" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Cerrar</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL DE EDITAR CHOFER Y DESTINO EN UN DESPACHO -->
<div class="modal fade"  id="editarChoferDestinoDespachoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edicion de Choferes y Destinos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="correlativo_editar" name="correlativo_editar" value="">

                <div class="alert alert-warning alert-dismissible" id="alert_editar_despacho">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> ATENCION! faltan campos por rellenar</h5>
                </div>

                <label>Destino</label>
                <input type="text" class="form-control input-sm" maxlength="20" id="destino_editar" name="destino_editar" placeholder="Ingrese destino" required>
                <br />
                <label>Fecha del Despacho</label>
                <input type="date" class="form-control" id="fecha_editar" name="fecha_editar" >
                <br />
                <label>Chofer</label>
                <select class="form-control custom-select" id="chofer_editar" name="chofer_editar" style="width: 100%;" >
                    <option name="" value="">Seleccione</option>
                </select>
                <br />
                <br />
                <label>Vehiculo</label>
                <select class="form-control custom-select" id="vehiculo_editar" name="vehiculo_editar" style="width: 100%;" >
                    <option value="">Seleccione</option>
                </select>
                <br />
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <button type="button" name="action" id="btnGuardar" class="btn btn-success pull-left" value="" onclick="modalGuardarEditarDespacho()">Guardar</button>
                    <button type="button" class="btn btn-danger float-right" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE EDITAR UNA FACTURA EN DESPACHO -->
<div class="modal fade"  id="editarFacturaEnDespachoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edicion de Facturas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="alert alert-warning alert-dismissible" id="alert_editar_documento">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> ATENCION! faltan campos por rellenar</h5>
                </div>

                <label>Numero de Documento</label>
                <input type="text" class="form-control input-sm" maxlength="20" id="documento_editar" name="documento_editar" placeholder="Ingrese numero de documento">

                <input  type="hidden" id="viejo_documento_editar" name="viejo_documento_editar"  />
                <input  type="hidden" id="correlativo_del_documento_editar" name="correlativo_del_documento_editar"  />
                <br />
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <button type="button" name="action" id="btnGuardar" class="btn btn-success pull-left" value="" onclick="modalGuardarDocumentoEnDespacho()">Guardar</button>
                    <button type="button" class="btn btn-danger float-right" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>