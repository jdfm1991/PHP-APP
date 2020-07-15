
<!-- MODAL  DETALLE DEL CLIENTE -->
<div class="modal fade" id="detallecliente" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detalles del Cliente: &nbsp;&nbsp <span id="descrip_cliente"></span> </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div id="datos_principales">
                    <p>
                        <strong>Codigo:</strong>&nbsp;&nbsp; <span id="codclient"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                        <strong>Razón Social:</strong>&nbsp;&nbsp; <span id="descrip"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                        <strong>RUTA:</strong>&nbsp; <span id="edv"></span>
                    </p>
                    <p>
                        <strong>Dirección:</strong>&nbsp;&nbsp; <span id="direccion"></span>
                    </p>
                    <p>
                        <strong>Saldo:</strong>&nbsp;&nbsp; <span id="saldo"></span>
                    </p>
                    <p>
                        <strong>Teléfonos:</strong>&nbsp;&nbsp; <span id="telefonos"></span>
                    </p>
                    <p>
                        <strong>Dias de Credito:</strong>&nbsp;&nbsp; <span id="diascredito"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                        <strong>Limite Credito:</strong>&nbsp; <span id="limitecredito"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                        <strong>Descuento %:</strong>&nbsp;&nbsp;<span id="descuento"></span>
                    </p>
                </div>

                <div id="datos_ultimo_pago_y_venta">
                    <p>
                        <strong>Ultima Venta:</strong>&nbsp;&nbsp; <span id="cod_documento_ultvent"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                        <strong>Monto:</strong>&nbsp;&nbsp; <span id="MtoTotal_ultvent"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                        <strong>Fecha:</strong>&nbsp;&nbsp; <span id="fechae_ultvent"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                        <strong>Realizada Por:</strong>&nbsp;&nbsp; <span id="codusua_ultvent"></span>
                    </p>

                    <p>
                        <strong>Ultimo Pago:</strong>&nbsp;&nbsp;&nbsp; <span id="cod_documento_ultpago"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                        <strong>Monto:</strong>&nbsp;&nbsp; <span id="monto_ultpago"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                        <strong>Fecha:</strong>&nbsp;&nbsp; <span id="fechae_ultpago"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                        <strong>Realizado Por:</strong>&nbsp;&nbsp; <span id="codusua_ultpago"></span>
                    </p>
                </div>
                <div id="tabla_facturas_pendientes">
                    <p>
                        <strong>Facturas Pendientes:</strong>
                    </p>
                    <div class="card-body" style="width:auto; overflow:scroll;">
                        <table id="relacion_facturas_pendientes" class="table table-bordered table-striped table-sm text-center">
                            <thead>
                            <tr>
                                <th class="small align-middle">Nro Factura</th>
                                <th class="small align-middle">Cod Vendedor</th>
                                <th class="small align-middle">Fecha Emisión</th>
                                <th class="small align-middle">Monto Bs</th>
                                <th class="small align-middle">Dias Transcurridos</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>

            <!-- BOX  LOADER -->
            <figure id="loader2">
                <div class="dot white"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </figure>

        </div>
    </div>
</div>