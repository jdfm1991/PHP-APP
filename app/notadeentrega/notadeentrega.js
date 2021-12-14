
var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;
}

function limpiar() {
    $("#nrodocumento").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function()
{
    ($("#nrodocumento").val() !== "")
        ? estado_minimizado = true : estado_minimizado = false ;
};

$(document).ready(function(){
    $("#nrodocumento").change( () => no_puede_estar_vacio() );
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {

    const documento = addZeros($("#nrodocumento").val());

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (documento !== "") {
            sessionStorage.setItem("nrodocumento", documento);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            $.ajax({
                url: "notadeentrega_controlador.php?op=buscar_notadeentrega",
                type: "post",
                dataType: "json",
                data: { nrodocumento: documento },
                beforeSend: function () {
                    SweetAlertLoadingShow();
                },
                error: function (e) {
                    isError = SweetAlertError(e.responseText.substring(0, 400) + "...", "Error!")
                    // send_notification_error(e.responseText);
                    console.log(e.responseText);
                },
                success: function (data) {
                    if(!jQuery.isEmptyObject(data)) {
                        let { empresa, cabecera, detalle } = data;

                        // datos empresa
                        if(!jQuery.isEmptyObject(empresa)) {
                            $('#descrip_empresa').text(empresa.descrip);
                            $('#rif_empresa').text(empresa.rif);
                            $('#direccion_empresa').text(empresa.direc1);
                            $('#telefono_empresa').text(empresa.telef);
                        }

                        // cabecera de la nota de entrega
                        if(!jQuery.isEmptyObject(cabecera)) {

                            $('#cabecera_codclie').text(cabecera.codclie);
                            $('#cabecera_rif').text(cabecera.rif);
                            $('#cabecera_codvend').text(cabecera.codvend);
                            $('#cabecera_rsocial').text(cabecera.rsocial);
                            $('#cabecera_representante').text(cabecera.representante);
                            $('#cabecera_telefono').text(cabecera.telefono);
                            $('#cabecera_fechae').text(cabecera.fechae);
                            $('#cabecera_direccion').text(cabecera.direccion);
                            $('#cabecera_direccion2').text(cabecera.direccion2);
                            $('#numeront').text(cabecera.numerod);

                            $('#tfoot_subtotal').text(cabecera.subtotal);
                            $('#tfoot_descuentototal').text(cabecera.descuento);
                            $('#tfoot_observacion').text(cabecera.notas1);
                            $('#tfoot_totalnota').text(cabecera.total);
                        }

                        if(!jQuery.isEmptyObject(detalle)) {
                            $.each(tabla, function(idx, opt) {
                                $('#notadeentrega_data').append(
                                    '<tr>' +
                                        '<td align="center" class="align-middle">' + opt.num + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.fechaemision + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.rifcliente + '</td>' +
                                        '<td align="center" class="text-left">' + opt.nombre + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.tipodoc + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.numerodoc + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.nroctrol + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.tiporeg + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.factafectada + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.nroretencion + '</td>' +
                                        '<td align="center" class="text-right">' + opt.totalventasconiva + '</td>' +
                                        '<td align="center" class="text-right">' + opt.mtoexento + '</td>' +
                                        '<td align="center" class="text-right">' + opt.base_imponible + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.alicuota_contribuyeiva+ '%</td>' +
                                        '<td align="center" class="text-right">' + opt.montoiva_contribuyeiva + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.retencioniva + '</td>' +
                                    '</tr>'
                                );
                            });
                        }
                    }
                },
                complete: function () {
                    if (!isError) SweetAlertLoadingClose();
                    $("#tabla").show('');//MOSTRAMOS LA TABLA.
                    validarCantidadRegistrosTabla();
                    mostrar()
                    limpiar();//LIMPIAMOS EL SELECTOR.
                }
            });
            estado_minimizado = true;
        }
    } else {
        SweetAlertError('Debe ingresar un Numero de Nota de Entrega');
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    var vendedor = sessionStorage.getItem("vendedor", vendedor);
    if (fechai !== "" && fechaf !== "" && vendedor !== "") {
        window.location = "motivonoventa_excel.php?&fechai="+fechai+"&fechaf="+fechaf+"&vendedor="+vendedor;
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    var vendedor = sessionStorage.getItem("vendedor", vendedor);
    if (fechai !== "" && fechaf !== "" && vendedor !== "") {
        window.open('motivonoventa_pdf.php?&fechai='+fechai+'&fechaf='+fechaf+'&vendedor='+vendedor, '_blank');
    }
});

function mostrar() {

    var texto= 'Total de clientes: ';
    var cuenta =(tabla.rows().count());
    $("#cuenta").html(texto + cuenta);
}

init();
