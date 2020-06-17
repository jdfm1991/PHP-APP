var tabla_relacion_despachos;

var tabla_despachos;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    listarRelacionDespachos();

}

function limpiar() {
    /*$("#fecha").val("");
    $("#chofer").val("");
    $("#vehiculo").val("");
    $("#destino").val("");
    $("#factura").val("");
    registros_por_despachar = "";
    peso_max_vehiculo = 0;
    peso_acum_facturas = 0;
    valor_bg_progreso = "bg-success";*/
}

function limpiar_campo_factura() {
    $("#nrodocumento").val("");
    $("#detalle_despacho").html("");
}

function agregarCeros(fact, cantidad_ceros = 6){
    var cad_cero="";
    for(var i=0;i<(cantidad_ceros-fact.length);i++)
        cad_cero+=0;
    return cad_cero+fact;
}

function modalEditarDespachos(correlativo) {
    if (correlativo !== "") {

        //CABECERA DEL DESPACHO
        $("#detalle_en_editar_despacho").html("");
        $.post("despachosrelacion_controlador.php?op=buscar_cabeceraDespacho", {correlativo: correlativo}, function (data, status) {
            data = JSON.parse(data);
            $('#editarDespachoModal').modal('show');
            $("#correlativo").val(correlativo);
            $("#detalle_en_editar_despacho").html(data.mensaje);
        });

        //TABLA DE LAS FACTURAS DENTRO DE ESE DESPACHO
        $('#tabla_editar_despacho').dataTable({

            "aProcessing": true,//Activamos el procesamiento del datatables
            "aServerSide": true,//Paginación y filtrado realizados por el servidor
            "ajax":
                {
                    beforeSend: function () {
                        $("#loader_editar_despacho").show(''); //MOSTRAMOS EL LOADER.
                    },
                    url: 'despachosrelacion_controlador.php?op=listar_despacho_por_correlativo',
                    type: "post",
                    data: {correlativo: correlativo},
                    dataType: "json",
                    error: function (e) {
                        console.log(e.responseText);
                    },
                    complete: function () {

                        // $("#tabla_detalle_despacho").show('');//MOSTRAMOS LA TABLA.
                        $("#loader_editar_despacho").hide();//OCULTAMOS EL LOADER.

                    }
                },//TRADUCCION DEL DATATABLE.
            "bDestroy": true,
            "responsive": true,
            "bInfo": true,
            "iDisplayLength": 5,//Por cada 5 registros hace una paginación
            "order": [[0, "desc"]],//Ordenar (columna,orden)
            'columnDefs':[{
                "targets": 3, // your case first column
                "className": "text-center",
                // "width": "4%"
            }],
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            }//cerrando language

        }).DataTable();
    } else {
        $("#detalle_en_editar_despacho").html("");
    }
}

function modalMostrarEditarDespacho(correlativo) {
    $('#alert_editar_despacho').hide();
    $.post("despachosrelacion_controlador.php?op=buscar_cabeceraDespacho_para_editar", {correlativo: correlativo}, function (data, status) {
        data = JSON.parse(data);
        $("#destino_editar").val(data.destino);
        $("#fecha_editar").val(data.fecha);
        $("#chofer_editar").html(data.chofer);
        $("#vehiculo_editar").html(data.vehiculo);
        $("#correlativo_editar").val(correlativo);

        $('#editarChoferDestinoDespachoModal').modal('show');
    });
}

function modalGuardarEditarDespacho() {

    var destino = $("#destino_editar").val();
    var fechad = $("#fecha_editar").val();
    var chofer = $("#chofer_editar").val();
    var vehiculo = $("#vehiculo_editar").val();
    var correlativo = $("#correlativo_editar").val();

    if(destino.length > 0 && fechad.length > 0 && chofer.length > 0 && vehiculo.length >0){
        $('#editarChoferDestinoDespachoModal').modal('hide');
        $.post("despachosrelacion_controlador.php?op=actualizar_cabeceraDespacho_para_editar", {correlativo: correlativo, destino: destino, fechad: fechad, chofer: chofer, vehiculo: vehiculo}, function (data, status) {
            data = JSON.parse(data);
            if(!data.mensaje.includes('ERROR')){
                modalEditarDespachos(correlativo);
                $('#editarChoferDestinoDespachoModal').modal('hide');
                $('#relacion_data').DataTable().ajax.reload();
            } else {
                $('#alert_editar_despacho').show();
            }
        });
    } else {
        $('#alert_editar_despacho').show();
    }
}

function modalMostrarDocumentoEnDespacho(nro_documento, correlativo) {
    $('#alert_editar_documento').hide();
    $("#documento_editar").val(agregarCeros(nro_documento));
    $("#viejo_documento_editar").val(agregarCeros(nro_documento));
    $("#correlativo_del_documento_editar").val(correlativo);
    $('#editarFacturaEnDespachoModal').modal('show');
}

function modalGuardarDocumentoEnDespacho() {

    var documento_nuevo = $("#documento_editar").val();
    var documento_viejo = $("#viejo_documento_editar").val();
    var correlativo = $("#correlativo_del_documento_editar").val();

    if(documento_nuevo.length > 0 && documento_viejo.length > 0 && correlativo.length > 0){
        $.post("despachosrelacion_controlador.php?op=actualizar_factura_en_despacho", {correlativo: correlativo, documento_nuevo: documento_nuevo, documento_viejo: documento_viejo}, function (data, status) {
            data = JSON.parse(data);
            if(!data.mensaje.includes('ATENCION!') && !data.mensaje.includes('ERROR')){
                $('#editarFacturaEnDespachoModal').modal('hide');
                $('#tabla_editar_despacho').DataTable().ajax.reload();
            } else {
                $('#alert_editar_documento').show();
                $('#text_alert_editar_documento').text(data.mensaje);
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
                data: {correlativo: correlativo, nro_documento: nro_documento},
                success: function (data) {
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
    var documento_agregar = agregarCeros($("#documento_agregar").val());

    if(correlativo.length > 0 && documento_agregar.length > 0){
        $.post("despachosrelacion_controlador.php?op=agregar_factura_en_despacho", {correlativo: correlativo, documento_agregar: documento_agregar}, function (data, status) {
            data = JSON.parse(data);
            if(!data.mensaje.includes('ATENCION!') && !data.mensaje.includes('ERROR')){
                $('#agregarFacturaEnDespachoModal').modal('hide');
                $('#tabla_editar_despacho').DataTable().ajax.reload();
                $('#relacion_data').DataTable().ajax.reload();
            } else {
                $('#alert_agregar_documento').show();
                $('#text_alert_agregar_documento').text(data.mensaje);
            }
        });
    } else {
        $('#alert_agregar_documento').show();
    }
}


function EliminarUnDespacho(correlativo) {

    Swal.fire({
        // title: '¿Estas Seguro?',
        text: "¿Estas Seguro de Eliminar el despacho "+agregarCeros(correlativo, 8)+" ?",
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
                data: {correlativo: correlativo},
                success: function (data) {
                    data = JSON.parse(data);
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                    Toast.fire({
                        icon: data.icono,
                        title: data.mensaje
                    });

                    //verifica si el mensaje de insercion no contiene error
                    if(!data.mensaje.includes('ERROR')) {
                        $('#relacion_data').DataTable().ajax.reload();
                    }
                }
            });
        }
    })
}

function listarRelacionDespachos() {
    tabla_relacion_despachos = $('#relacion_data').dataTable({

        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax":
            {
                url: 'despachosrelacion_controlador.php?op=listar_RelacionDespachos',
                type: "get",
                dataType: "json",
                error: function (e) {
                    console.log(e.responseText);
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
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }//cerrando language

    }).DataTable();
}

function buscarFacturaEnDespachos(nrofact){
    if (nrofact !== "") {
        nrofact = agregarCeros(nrofact);
        $("#detalle_despacho").html("");
        $.post("despachosrelacion_controlador.php?op=buscar_facturaEnDespachos", {nrfactb: nrofact}, function(data, status){
            data = JSON.parse(data);
            $("#detalle_despacho").html(data.mensaje);
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

init();