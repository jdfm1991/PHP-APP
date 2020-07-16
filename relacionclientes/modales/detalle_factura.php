<!-- MODAL  DETALLE DE FACTURA -->
<div class="modal fade" id="detallefactura" role="dialog" style="z-index: 1600;">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detalles de la Factura #: &nbsp;&nbsp; <span id="numero_factura"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <strong>Razón Social:</strong>&nbsp;&nbsp; <span id="descrip_detfactura"></span><br/>
                    <strong>Facturado Por:</strong>&nbsp;&nbsp; <span id="codusua_detfactura"></span><br/>
                    <strong>Fecha de Emisión:</strong>&nbsp;&nbsp; <span id="fechae_detfactura"></span><?php /*echo date("d/m/Y h:iA", strtotime($j['fechae'])); */ ?>&nbsp; &nbsp;
                    <strong>Ruta:</strong>&nbsp;&nbsp; <span id="codvend_detfactura"></span>
                </p>
                <div style="width:auto; overflow:scroll;">
                    <table id="tabla_detalle_factura" class="table table-bordered table-striped table-sm text-center ">
                        <thead>
                        <tr>
                            <th class="small"><strong>Codigo</strong></th>
                            <th class="small"><strong>Descripción</strong></th>
                            <th class="small"><strong>Cantidad</strong></th>
                            <th class="small"><strong>Unidad</strong></th>
                            <th class="small"><strong>Monto Bs</strong></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        /*$numerod = $j['numerod'];
                        $tipofact = 'A';
                        $query = $bd1->consultaDetalleFactura($numerod, $tipofact);
                        foreach ($query as $f) {
                            $paquetes = 0;
                        }

                        $bultos = 0;
                        $cantidad = 0;
                        {*/
                        ?>
                        <tr>
                            <td align="center" class="small align-middle"><?php /*echo $f['CodItem']; */ ?></td>
                            <td align="center" class="small align-middle"><?php /*echo $f['Descrip1'];*/ ?></td>
                            <td align="center"
                                class="small align-middle"><?php /*echo $cantidad = number_format($f['cantidad'], 0, ".", ",");*/ ?></td>
                            <td align="center" class="small align-middle"><?php /*if ($f['esunid'] == 1) {
                                            echo "PAQ";
                                            $paquetes = $paquetes + $cantidad;} else {
                                            echo "BUL";
                                            $bultos = $bultos + $cantidad;}*/ ?></td>
                            <td align="center"
                                class="small align-middle"><?php /*echo number_format($f['TotalItem'], 2, ",", ".");*/ ?></td>
                        </tr>
                        <?php /*}*/ ?>
                        <tr>
                            <td colspan="5">===================================================================</td>
                        </tr>
                        <?php
                        /*
                                                    $query = $bd1->consultaFactura($numerod, $tipofact);
                                                    foreach ($query as $g) {*/
                        ?>
                        <!--<tr>
                                <td class="small align-middle"><div >Total de Bultos <?php /*echo $bultos; */ ?></div></td>
                                <td colspan="2"></td>
                                <td class="small align-middle"><div align="right">Sub Total</div></td>
                                <td class="small align-middle" ><div align="center"><?php /*echo number_format($g['subtotal'], 2, ",", "."); */ ?></div></td>
                            </tr>
                            <tr>
                                <td class="small align-middle"><div >Total de Paquetes <?php /*echo $paquetes; */ ?></div></td>
                                <td colspan="2"></td>
                                <td class="small align-middle"><div align="right">Descuento</div></td>
                                <td class="small align-middle"><div align="center"><?php /*echo number_format($g['descuento'], 2, ",", "."); */ ?></div></td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td class="small align-middle"><div align="right">Excento</div></td>
                                <td class="small align-middle"><div align="center"><?php /*echo number_format($g['exento'], 2, ",", "."); */ ?></div></td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td class="small align-middle"><div align="right">Base Imponible</div></td>
                                <td class="small align-middle"><div align="center"><?php /*echo number_format($g['base'], 2, ",", "."); */ ?></div></td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td class="small align-middle"><div align="right">Impuestos <?php /*echo number_format($g['iva'], 0, ".", ",") . " %"; */ ?></div></td>
                                <td class="small align-middle"><div align="center"><?php /*echo number_format($g['impuesto'], 2, ",", "."); */ ?></div></td>
                            </tr>
                            <tr>
                                <td  colspan="3"><div align="right"></div></td>
                                <td class="small align-middle"><div align="right">Monto Total</div></td>
                                <td class="small align-middle"><div align="center"><?php /*echo number_format($g['total'], 2, ",", "."); */ ?></div></td>
                            </tr>-->
                        </tbody>
                    </table>
                </div>
                <?php
                /*$cuenta = $bd->revisaDespachos($numerod);
                if ($cuenta == 0) {
                    echo "Factura Sin Despachar";
                } else {
                    $query = $bd->getDespachos($numerod);
                    foreach ($query as $i) {
                        echo "Factura Despachada: " . date("d/m/Y", strtotime($i['fechad'])) . "</br> Por: " . $i['nomper'] . "</br>En el Despacho nro: " . str_pad($i['correlativo'], 8, 0, STR_PAD_LEFT);
                    }
                }
                */ ?>
            </div>
            <?php /*}
        }
      }
    }*/
            ?>
            <!-- BOX  LOADER -->
            <figure id="loader3">
                <div class="dot white"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </figure>
        </div>
    </div>
</div>