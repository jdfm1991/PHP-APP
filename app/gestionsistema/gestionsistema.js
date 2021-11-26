
let tabla_parametros;

function init() {
    //cuando se da click al boton submit entonces se ejecuta la funcion guardaryeditar(e);
    $("#modulo_form").on("submit", function (e) {
        guardarModulo(e);
    });

    $("#custom-tabs-one-tab li a").on("click", function (e) {

        switch (this.id.substr(11, 35)) {
            case 'parametros':
                listar_parametros();
                break;
            case 'notificaciones':
                break;
        }
    });

    $("#custom-tab-parametros").trigger("click");
}

/*funcion para limpiar formulario de modal*/
function limpiar_modulo() {
    $('#name_modulo').val('');
    $('#modulo_form')[0].reset();
    $(".modal-title").text("Agregar Módulo");
    $('#modulo').html('<option value="">--Seleccione--</option>');
}

$(document).ready(function () {
    //
});

//function listar
function listar_parametros() {
    let isError = false;
    $.ajax({
        url: "gestionsistema_controlador.php?op=listar_modulos_json",
        type: "get",
        dataType: "json",
        beforeSend: function () {
            SweetAlertLoadingShow();
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function(data) {
            if(!jQuery.isEmptyObject(data)) {
                let { array_data, contenido_tabla } = data;

                array_data_from_json = array_data;

                //TABLA
                tabla_parametros = $('#parametro_data').dataTable({
                    "aProcessing": true,//Activamos el procesamiento del datatables
                    "aServerSide": true,//Paginación y filtrado realizados por el servidor

                    "sEcho": contenido_tabla.sEcho, //INFORMACION PARA EL DATATABLE
                    "iTotalRecords": contenido_tabla.iTotalRecords, //TOTAL DE REGISTROS AL DATATABLE.
                    "iTotalDisplayRecords": contenido_tabla.iTotalDisplayRecords, //TOTAL DE REGISTROS A VISUALIZAR.
                    "aaData": contenido_tabla.aaData, // informacion por registro

                    "bDestroy": true,
                    "responsive": true,
                    "bInfo": true,
                    "iDisplayLength": 10,//Por cada 10 registros hace una paginación
                    "order": [[0, "asc"]],//Ordenar (columna,orden)
                    "language": texto_español_datatables
                }).DataTable();
            }
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        },
    });
}

function mostrar_parametros(name_modulo= "") {
    let isError = false;

    $('#detalleparametroModal').modal('show');

    $('#relacion_detalle_parametros').dataTable({
        "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
        "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
        "ajax": {
            url: "gestionsistema_controlador.php?op=mostrar_parametros_modal",
            type: "post",
            dataType: "json",
            data: {name_modulo: name_modulo},
            beforeSend: function () {
                SweetAlertLoadingShow();
            },
            error: function (e) {
                isError = SweetAlertError(e.responseText, "Error!")
                send_notification_error(e.responseText);
                console.log(e.responseText);
            },
            complete: function () {
                if(!isError) SweetAlertLoadingClose();
            }
        },//TRADUCCION DEL DATATABLE.
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]],
        "language": texto_español_datatables
    });
}

function mostrar_modulo(name_modulo= -1) {
    let isError = false;
    limpiar_modulo();

    $('#moduloModal').modal('show');

    $.ajax({
        url: "gestionsistema_controlador.php?op=mostrar_modulo",
        method: "POST",
        dataType: "json",
        data: {name_modulo: name_modulo},
        beforeSend: function () {
            SweetAlertLoadingShow();
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            //lista de seleccion
            $.each(data.lista_modulos, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#modulo').append('<option name="" value="' + opt +'">' + opt.substr(0, 35) + '</option>');
            });

            if(name_modulo !== -1) {
                $('.modal-title').text("Editar Módulo");
                $('#name_modulo').val(name_modulo);
            }

        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

//la funcion guardaryeditar(e); se llama cuando se da click al boton submit
function guardarModulo(e) {

    e.preventDefault(); //No se activará la acción predeterminada del evento
    let modulo = $('#modulo_form #modulo').val();
    const formData = new FormData($("#modulo_form")[0]);

    console.log(modulo,formData)
    if (modulo !== "") {
        $.ajax({
            url: "gestionsistema_controlador.php?op=guardar_modulo",
            type: "POST",
            data: {
                name_modulo: modulo,
                array_data: array_data_from_json,
            },
            dataType: "json",
            error: function (e) {
                SweetAlertError(e.responseText, "Error!")
                send_notification_error(e.responseText);
                console.log(e.responseText);
            },
            success: function (data) {
                let { icono, mensaje } = data
                ToastSweetMenssage(icono, mensaje);

                //verifica si el mensaje de insercion contiene error
                if(mensaje.includes('error')) {
                    return (false);
                } else {
                    $('#modulo_form')[0].reset();
                    $('#moduloModal').modal('hide');
                    listar_parametros();
                    limpiar_modulo();
                }
            }
        });
    } else {
        SweetAlertError('Debe seleccionar un MÓDULO para continuar.');
        return (false);
    }
}

function eliminar_modulo(name_modulo) {

    Swal.fire({
        // title: '¿Estas Seguro?',
        text: "¿Estas Seguro de Eliminar el módulo ("+name_modulo+") ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "gestionsistema_controlador.php?op=eliminar_modulo",
                method: "POST",
                dataType: "json",
                data: {name_modulo: name_modulo},
                error: function (e) {
                    SweetAlertError(e.responseText, "Error!")
                    send_notification_error(e.responseText);
                    console.log(e.responseText);
                },
                success: function (data) {
                    ToastSweetMenssage(data.icono, data.mensaje);
                    // $('#modulo_data').DataTable().ajax.reload();
                    listar_parametros();
                }
            });
        }
    })
}

//Mostrar datos del usuario en la ventana modal del formularioS
init();






