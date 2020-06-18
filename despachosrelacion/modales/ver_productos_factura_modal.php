

<!-- MODAL DETALLE DE UN DESPACHO DESPACHO -->
<div class="modal fade"  id="verDetalleDeUnDespachoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle del Despacho</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="correlativo_ver_productos_despacho" name="correlativo_ver_productos_despacho" value="">

                <!-- productos del despacho -->
                <div class="card-body" style="width:auto;">
                    <table class="table table-bordered table-striped table-sm  text-center" style="width:100%;" id="tabla_detalle_productos_del_despacho">
                        <thead>
                        <tr>
                            <th>Código del Producto</th>
                            <th>Descripción</th>
                            <th>Cantidad de Bultos</th>
                            <th>Cantidad de Paquetes</th>
                        </tr>
                        </thead>
                        <tfoot style="background-color: #aaa;color: white;">
                        <tr>
                            <th></th>
                            <th>TOTAL = </th>
                            <th id="cantBul_tfoot">Cantidad de Bultos</th>
                            <th id="cantPaq_tfoot">Cantidad de Paquetes</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- BOX  LOADER -->
                <figure id="loader_detalle_productos_despacho">
                    <div class="dot white"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </figure>
                <div class="modal-footer">
                    <div class="col-md-12">
                        <button class="btn btn-primary pull-left" id="exportarDetalleDespacho_pdf"  onclick="" type="button">Exportar a PDF</button>
                        <button type="button" class="btn btn-danger float-right" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Cerrar</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
