<div class="modal fade"  id="editarDespachoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Despachos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <br class="modal-body">
                <div id="detalle_en_editar_despacho"></div>
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
<!--                    <button type="button" name="action" id="btnBuscarFactModal" class="btn btn-success pull-right" value="Add">Buscar</button>-->
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>