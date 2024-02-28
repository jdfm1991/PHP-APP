<!-- MODAL DETALLE DE UN DESPACHO DESPACHO -->
<html>

<div class="modal fade" id="Detalles_ventas_d" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle del Año <span id="detalle_anno"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro" id='selector_annos'>
                            <?php  for ($i = 2021; $i <=date("Y"); $i++) { ?>
                            <li class="active" onclick="miFunc(<?php echo $i ?>)" id="anno<?php echo $i ?>" value="<?php echo $i ?>"><a data-toggle="tab"  href="" ><i class=""></i>
                                    <?php echo $i ?></a>
                            </li>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                             <?php } ?>
                        </ul>
                    </div>






                <input type="hidden" id="ver_detalle_ventas_d" name="ver_detalle_ventas_d" value="">

                <!-- productos del despacho -->
                <div class="card-body" id="tabla">
                    <table class="table table-bordered table-striped table-sm  text-center" style="width:100%;" id="tabla_detalle_ventas_d">
                        <thead>
                            <tr>
                                <th>Meses</th>
                                <th>Facturas $</th>
                                <th>Notas Entregas $</th>
                                <th>Cantidad de Unidades</th>
                                <th>Cantidad de Paquetes</th>
                                <th>Valorización Total $</th>
                                <th>Valorización Total Unidades</th>
                            </tr>
                        </thead>
                       <tfoot style="background-color: #aaa;color: white;">
                            <tr>

                             <th>TOTAL </th>
                            <th id="total_fact">Facturas $</th>
                            <th id="total_nota">Notas Entregas $</th>
                            <th id="cantidad_paq">Cantidad de Unidades</th>
                            <th id="cantidad_bul">Cantidad de Paquetes</th>
                            <th id="total_dolar">Valorización Total $</th>
                            <th id="total_unid">Valorización Total Unidades</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="modal-footer">
                    <div class="col-md-12">
                        <!--<button class="btn btn-primary pull-left" id="exportarDetalleDespacho_pdf"  onclick="" type="button">Exportar a PDF</button>
                        <button class="btn btn-primary pull-left" id="DetalladoDespacho_pdf" onclick="" type="button">PDF Detallado</button>-->
                        <button type="button" class="btn btn-danger float-right" data-dismiss="modal"><i
                                class="fa fa-times" aria-hidden="true"></i> Cerrar</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</html>