var tabla_relacion_despachos;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    listarRelacionDespachos();

    $("#modalMostrarEditarDespacho").on("click", function (e) {
        var correlativo = $('#correlativo').val();
        modalMostrarEditarDespacho(correlativo);
    });

}

function limpiar_campo_factura() {
    $("#nrodocumento").val("");
    $("#detalle_despacho").html("");
}

function limpiar_modal_detalle_despacho() {
    $('#tabla_editar_despacho tbody').empty();
    $("#correlativo").val("");
    $("#correl").text("");
    $("#Destino").text("");
    $("#fechad").text("");
    $("#vehiculo").text("");
    $("#cantFacturas").text("");
}

function modalEditarDespachos(correlativo) { //editar
    let isError = false;
    limpiar_modal_detalle_despacho();
    if (correlativo !== "") {
        $.ajax({
            url: "despachosrelacion_controlador.php?op=buscar_despacho_por_correlativo",
            method: "POST",
            dataType: "json",
            data: { correlativo: correlativo },
            beforeSend: function () {
                $('#editarDespachoModal').modal('show');
                $('#relacion_despacho_editar').hide();
                $('#modalMostrarEditarDespacho').hide();
                SweetAlertLoadingShow();
            },
            error: function (e) {
                isError = SweetAlertError(e.responseText, "Error!")
                console.log(e.responseText);
            },
            success: function (data) {

                //CABECERA DEL DESPACHO
                $("#correlativo").val(correlativo);
                $("#correl").text(data.correl);
                $("#Destino").text(data.Destino);
                $("#fechad").text(data.fechad);
                $("#vehiculo").text(data.vehiculo);
                $("#cantFacturas").text(data.cantFacturas);
                $('#modalMostrarEditarDespacho').show();

                //TABLA DE DETALLE DE DESPACHO
                $('#tabla_editar_despacho').dataTable({
                    "aProcessing": true,//Activamos el procesamiento del datatables
                    "aServerSide": true,//Paginación y filtrado realizados por el servidor

                    "sEcho": data.tabla.sEcho, //INFORMACION PARA EL DATATABLE
                    "iTotalRecords": data.tabla.iTotalRecords, //TOTAL DE REGISTROS AL DATATABLE.
                    "iTotalDisplayRecords": data.tabla.iTotalDisplayRecords, //TOTAL DE REGISTROS A VISUALIZAR.
                    "aaData": data.tabla.aaData, // informacion por registro

                    "bDestroy": true,
                    "responsive": true,
                    "bInfo": true,
                    "iDisplayLength": 5,//Por cada 5 registros hace una paginación
                    "order": [[0, "desc"]],//Ordenar (columna,orden)
                    'columnDefs':[{
                        "targets": 3, // your case first column
                        "className": "text-center"
                    }],
                    "language": texto_español_datatables
                }).DataTable();

                $('#relacion_despacho_editar').show();
                $("#loader_editar_despacho").hide('');
            },
            complete: function () {
                if(!isError) SweetAlertLoadingClose();
            }
        });
    }
}

function modalMostrarEditarDespacho(correlativo) {
    let isError = false;
    $('#alert_editar_despacho').hide();
    $.ajax({
        url: "despachosrelacion_controlador.php?op=buscar_cabeceraDespacho_para_editar",
        method: "POST",
        dataType: "json",
        data: {correlativo: correlativo},
        beforeSend: function () {
            SweetAlertLoadingShow();
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            //lista de seleccion de chofer
            $('#chofer_editar').append('<option name="" value="">Seleccione</option>');
            $.each(data.lista_choferes, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#chofer_editar').append('<option name="" value="' + opt.Cedula +'">' + opt.Nomper + '</option>');
            });

            //lista de seleccion de vehiculos
            $('#vehiculo_editar').append('<option name="" value="">Seleccione</option>');
            $.each(data.lista_vehiculos, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#vehiculo_editar').append('<option name="" value="' + opt.ID +'">' + opt.Modelo + '  ' + opt.Capacidad + ' Kg' + '</option>');
            });

            $("#destino_editar").val(data.destino);
            $("#fecha_editar").val(data.fecha);
            $("#chofer_editar").val(data.chofer);
            $("#vehiculo_editar").val(data.vehiculo);
            $("#correlativo_editar").val(correlativo);
            $('#editarChoferDestinoDespachoModal').modal('show');
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

function modalGuardarEditarDespacho() {
    let isError = false;
    var destino = $("#destino_editar").val();
    var fechad = $("#fecha_editar").val();
    var chofer = $("#chofer_editar").val();
    var vehiculo = $("#vehiculo_editar").val();
    var correlativo = $("#correlativo_editar").val();

    if(destino.length > 0 && fechad.length > 0 && chofer.length > 0 && vehiculo.length >0){
        $('#editarChoferDestinoDespachoModal').modal('hide');
        $.ajax({
            url: "despachosrelacion_controlador.php?op=actualizar_cabeceraDespacho_para_editar",
            method: "POST",
            dataType: "json",
            data: {correlativo: correlativo, destino: destino, fechad: fechad, chofer: chofer, vehiculo: vehiculo},
            beforeSend: function () {
                SweetAlertLoadingShow();
            },
            error: function (e) {
                isError = SweetAlertError(e.responseText, "Error!")
                console.log(e.responseText);
            },
            success: function (data) {
                if(!data.mensaje.includes('ERROR')){
                    modalEditarDespachos(correlativo);
                    $('#editarChoferDestinoDespachoModal').modal('hide');
                    $('#relacion_data').DataTable().ajax.reload();
                } else {
                    $('#alert_editar_despacho').show();
                }
            },
            complete: function () {
                if(!isError) SweetAlertLoadingClose();
            }
        });
    } else {
        $('#alert_editar_despacho').show();
    }
}

function modalMostrarDocumentoEnDespacho(nro_documento, correlativo) {
    $('#alert_editar_documento').hide();
    $("#documento_editar").val(addZeros(nro_documento));
    $("#viejo_documento_editar").val(addZeros(nro_documento));
    $("#correlativo_del_documento_editar").val(correlativo);
    $('#editarFacturaEnDespachoModal').modal('show');
}

function modalGuardarDocumentoEnDespacho() {
    let isError = false;
    var documento_nuevo = $("#documento_editar").val();
    var documento_viejo = $("#viejo_documento_editar").val();
    var correlativo = $("#correlativo_del_documento_editar").val();

    if(documento_nuevo.length > 0 && documento_viejo.length > 0 && correlativo.length > 0){
        $.ajax({
            url: "despachosrelacion_controlador.php?op=actualizar_factura_en_despacho",
            method: "POST",
            dataType: "json",
            data: {correlativo: correlativo, documento_nuevo: documento_nuevo, documento_viejo: documento_viejo},
            beforeSend: function () {
                SweetAlertLoadingShow();
            },
            error: function (e) {
                isError = SweetAlertError(e.responseText, "Error!")
                console.log(e.responseText);
            },
            success: function (data) {
                if(!data.mensaje.includes('ATENCION!') && !data.mensaje.includes('ERROR')){
                    $('#editarFacturaEnDespachoModal').modal('hide');
                    $('#tabla_editar_despacho').DataTable().ajax.reload();
                } else {
                    $('#alert_editar_documento').show();
                    $('#text_alert_editar_documento').text(data.mensaje);
                }
            },
            complete: function () {
                if(!isError) SweetAlertLoadingClose();
            }
        });
    } else {
        $('#alert_editar_documento').show();
    }
}

function modalEliminarDocumentoEnDespacho(nro_documento, correlativo) {

    Swal.fire({
        // title: '¿Estas Seguro?',
        text: "¿Estas Seguro de Eliminar el documento "+nro_documento+" ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "despachosrelacion_controlador.php?op=eliminar_factura_en_despacho",
                method: "POST",
                dataType: "json",
                data: {correlativo: correlativo, nro_documento: nro_documento},
                error: function (e) {
                    SweetAlertError(e.responseText, "Error!")
                    console.log(e.responseText);
                },
                success: function (data) {
                    SweetAlertSuccessLoading(data.mensaje);
                    $('#tabla_editar_despacho').DataTable().ajax.reload();
                }
            });
        }
    })
}

function modalAgregarDocumentoEnDespacho() {
    $('#alert_agregar_documento').hide();
    $('#agregarFacturaEnDespachoModal').modal('show');
}

function modalGuardarNuevoDocumentoEnDespacho() {

    var correlativo = $("#correlativo").val();
    var documento_agregar = addZeros($("#documento_agregar").val());

    if(correlativo.length > 0 && documento_agregar.length > 0){
        $.ajax({
            url: "despachosrelacion_controlador.php?op=agregar_factura_en_despacho",
            method: "POST",
            dataType: "json",
            data: {correlativo: correlativo, documento_agregar: documento_agregar},
            error: function (e) {
                SweetAlertError(e.responseText, "Error!")
                console.log(e.responseText);
            },
            success: function (data) {
                if(!data.mensaje.includes('ATENCION!') && !data.mensaje.includes('ERROR')){
                    $('#agregarFacturaEnDespachoModal').modal('hide');
                    $('#tabla_editar_despacho').DataTable().ajax.reload();
                    $('#relacion_data').DataTable().ajax.reload();
                } else {
                    $('#alert_agregar_documento').show();
                    $('#text_alert_agregar_documento').text(data.mensaje);
                }
            }
        });
    } else {
        $('#alert_agregar_documento').show();
    }
}


function EliminarUnDespacho(correlativo) {

    Swal.fire({
        // title: '¿Estas Seguro?',
        text: "¿Estas Seguro de Eliminar el despacho "+addZeros(correlativo, 8)+" ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "despachosrelacion_controlador.php?op=eliminar_un_despacho",
                method: "POST",
                dataType: "json",
                data: {correlativo: correlativo},
                error: function (e) {
                    SweetAlertError(e.responseText, "Error!")
                    console.log(e.responseText);
                },
                success: function (data) {
                    ToastSweetMenssage(data.icono, data.mensaje)
                    //verifica si el mensaje de insercion no contiene error
                    if(!data.mensaje.includes('ERROR')) {
                        $('#relacion_data').DataTable().ajax.reload();
                    }
                }
            });
        }
    })
}

function modalVerDetalleDespacho(correlativo) {
    if (correlativo !== "") {
        let isError = false;
        $("#correlativo_ver_productos_despacho").val(correlativo);
        $("#nro_despacho").text(addZeros(correlativo, 8));
        $('#verDetalleDeUnDespachoModal').modal('show');

        $.ajax({
            url: "despachosrelacion_controlador.php?op=listar_productos_de_un_despacho",
            method: "post",
            dataType: "json",
            data: {correlativo: correlativo},
            beforeSend: function () {
                SweetAlertLoadingShow();
            },
            error: function (e) {
                isError = SweetAlertError(e.responseText, "Error!")
                console.log(e.responseText);
            },
            success: function (data) {

                //TABLA DE LAS FACTURAS DENTRO DE ESE DESPACHO
                $('#tabla_detalle_productos_del_despacho').dataTable({
                    "aProcessing": true,//Activamos el procesamiento del datatables
                    "aServerSide": true,//Paginación y filtrado realizados por el servidor

                    "sEcho": data.tabla.sEcho, //INFORMACION PARA EL DATATABLE
                    "iTotalRecords": data.tabla.iTotalRecords, //TOTAL DE REGISTROS AL DATATABLE.
                    "iTotalDisplayRecords": data.tabla.iTotalDisplayRecords, //TOTAL DE REGISTROS A VISUALIZAR.
                    "aaData": data.tabla.aaData, // informacion por registro

                    "bDestroy": true,
                    "responsive": true,
                    "bInfo": true,
                    "iDisplayLength": 8,//Por cada 8 registros hace una paginación
                    "order": [[0, "desc"]],//Ordenar (columna,orden)
                    'columnDefs':[{
                        "targets": 3, // your case first column
                        "className": "text-center",
                    }],
                    "language": texto_español_datatables
                }).DataTable();

                $("#cantBul_tfoot").text(data.total_bultos);
                $("#cantPaq_tfoot").text(data.total_paq);
                $("#loader_detalle_productos_despacho").hide();//OCULTAMOS EL LOADER.
            },
            complete: function () {
                if(!isError) SweetAlertLoadingClose();
            }
        });
    }
}

function abrirReporteProductosDeUnDepacho(correlativo) {
    if (correlativo !== "") {
        window.open('../despachos/despachos_pdf.php?&correlativo=' + correlativo, '_blank');
    }
}

function abrirReporteDetalleCompletoDeUnDepacho(correlativo) {
    if (correlativo !== "") {
        window.open('despachosrelacion_detalle_pdf.php?&correlativo=' + correlativo, '_blank');
    }
}

function listarRelacionDespachos() {
    let isError = false;
    tabla_relacion_despachos = $('#relacion_data').dataTable({
        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax":
            {
                url: 'despachosrelacion_controlador.php?op=listar_RelacionDespachos',
                type: "get",
                dataType: "json",
                beforeSend: function () {
                    SweetAlertLoadingShow();
                },
                error: function (e) {
                    isError = SweetAlertError(e.responseText, "Error!")
                    console.log(e.responseText);
                },
                complete: function () {
                    if(!isError) SweetAlertLoadingClose();
                }
            },

        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,//Por cada 10 registros hace una paginación
        "order": [[0, "desc"]],//Ordenar (columna,orden)
        'columnDefs':[{
                "targets": 3, // your case first column
                "className": "text-center",
                // "width": "4%"
        }],
        "language": texto_español_datatables
    }).DataTable();
}

function buscarFacturaEnDespachos(nrofact){
    if (nrofact !== "") {
        nrofact = addZeros(nrofact);
        $("#detalle_despacho").html("");
        $.ajax({
            url: "../despachos/despachos_controlador.php?op=buscar_facturaEnDespachos_modal",
            method: "POST",
            dataType: "json",
            data: {nrfactb: nrofact},
            error: function (e) {
                SweetAlertError(e.responseText, "Error!")
                console.log(e.responseText);
            },
            success: function (data) {
                if(!jQuery.isEmptyObject(data.factura_en_despacho)){
                    $("#detalle_despacho").append(
                        '<p>' +
                        '<strong>Nro de Documento: </strong>  ' + nrofact + '  ' +
                        '<strong>Despacho Nro: </strong>  ' + data.factura_en_despacho.Correlativo + '<br>' +
                        '<strong>Fecha Emision: </strong>  ' + data.factura_en_despacho.fechae + '  ' +
                        '<strong>Despacho Nro: </strong>  ' + data.factura_en_despacho.Destino +
                        '</p>'
                    );

                    if(!jQuery.isEmptyObject(data.datos_pago)){
                        $("#detalle_despacho").append(
                            '<p>' +
                            '<strong>PAGO: </strong>  ' + data.datos_pago.fecha_liqui +  '  ' +
                            '<strong>POR UN MONTO DE: </strong>  ' + data.datos_pago.monto_cancelado + ' BsS' +
                            '</p>'
                        );
                    }else{
                        $("#detalle_despacho").append('<br>DOCUMENTO NO LIQUIDADO');
                    }
                } else {
                    $("#detalle_despacho").append('<br>EL DOCUMENTO INGRESADO <strong>NO A SIDO DESPACHADO</strong>');
                }
            }
        });

    } else {
        $("#detalle_despacho").html("");
    }
}

//ACCION AL PRECIONAR EL BOTON BUSCAR.
$(document).on("click", "#btnBuscarFactModal", function () {
    var fact = $("#nrodocumento").val();
    buscarFacturaEnDespachos(fact);
});

//ACCION AL PRECIONAR EL BOTON EXPORTAR A PDF DE DETALLE PRODUCTOS EN UN DESPACHO.
$(document).on("click", "#exportarDetalleDespacho_pdf", function () {
    abrirReporteProductosDeUnDepacho(
        $("#correlativo_ver_productos_despacho").val()
    );
});

init();