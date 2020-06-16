<div class="modal fade"  id="buscarxfacturaModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buscar por Factura</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label>Nro de documento</label>
                <input type="text" class="form-control input-sm" maxlength="20" id="nrodocumento" name="nrodocumento" placeholder="Ingrese numero de documento" required >
                <br />
                <div id="detalle_despacho"></div>
                <div class="modal-footer">
                    <button type="button" onclick="limpiar_campo_factura()" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Cerrar</button>
                    <button type="button" name="action" id="btnBuscarFactModal" class="btn btn-success pull-right" value="Add">Buscar</button>
                </div>
            </div>

        </div>
    </div>
</div>