var tabla;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#spinner").css('visibility', 'hidden');
    listar();
}

/*funcion para limpiar formulario de modal*/
function limpiar() {
    $("#cedula").val("");
    $('#login').val("");
    $('#nomper').val("");
    $('#email').val("");
    $('#clave').val("");
    $('#rol').html("");
    $('#estado').val("");
    $('#id_usuario').val("");
}

//function listar
function listar() {
    tabla = $('#tabla').dataTable({
        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax":
            {
                url: 'kpimanager_controlador.php?op=listar',
                type: "post",
                dataType: "json",
                data: {edv: "-"},
                beforeSend: function () {
                    SweetAlertLoadingShow();
                },
                error: function (e) {
                    SweetAlertError(e.responseText.substring(0, 400) + "...", "Error!")
                    console.log(e.responseText);
                },
                complete: function () {
                    // validarCantidadRegistrosTabla();
                }
            },

        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,//Por cada 10 registros hace una paginación
        //"order": [[0, "asc"]],//Ordenar (columna,orden)
        "language": texto_español_datatables
    }).DataTable();
}

function cambiarEstado(id, est) {
    Swal.fire({
        title: '¿Estas Seguro?',
        text: "¿De realizar el cambio de estado?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, cambiar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "kpimanager_controlador.php?op=activarydesactivar",
                method: "POST",
                data: {id: id, est: est},
                success: function (data) {
                    $('#tabla').DataTable().ajax.reload();
                }
            });
        }
    })
}

function mostrar(edv) {

    limpiar();
    $('#kpimanagerModal').modal('show');
    loading()
    // $("#loader1").show('');

    /*$.post("kpimanager_controlador.php?op=mostrar", {id_usuario: id_usuario}, function (data, status) {
        data = JSON.parse(data);

        //lista de seleccion de roles
        $('#rol').append('<option name="" value="">Seleccione un rol de usuario</option>');
        $.each(data.lista_roles, function(idx, opt) {
            //se itera con each para llenar el select en la vista
            $('#rol').append('<option name="" value="' + opt.ID +'">' + opt.Descripcion.substr(0, 35) + '</option>');
        });

        $('#cedula').val(data.cedula);
        $("#cedula").prop("disabled", true);
        $('#login').val(data.login);
        $("#login").prop("disabled", false);
        $('#nomper').val(data.nomper);
        $("#nomper").prop("disabled", false);
        $('#email').val(data.email);
        $('#clave').val(data.clave);
        $('#rol').val(data.rol);
        $('#estado').val(data.estado);
        $('.modal-title').text("Editar Usuario");
        $('#id_usuario').val(id_usuario);

        // $("#loader1").hide();
    });*/
}

function guardaryeditar(e) {

    e.preventDefault(); //No se activará la acción predeterminada del evento
    var formData = new FormData($("#usuario_form")[0]);

    $.ajax({
        url: "kpimanager_controlador.php?op=guardar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            console.log(datos);
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            })
            Toast.fire({
                icon: 'success',
                title: 'Proceso Exitoso!'
            })
            $('#usuario_form')[0].reset();
            $('#usuarioModal').modal('hide');
            $('#usuario_data').DataTable().ajax.reload();
            limpiar();
        }
    });
}

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_guardar", function () {

    let form = $('#frm_kpimarcas').serialize();

    $.ajax({
        async: true,
        url: "kpimarcas_controlador.php?op=guardar_kpiMarcas",
        method: "POST",
        data: form,
        beforeSend: function () {
            $("#spinner").css('visibility', 'visible'); //MOSTRAMOS EL LOADER.
        },
        error: function (e) {
            console.log(e.responseText);
            Swal.fire('Atención!','ha ocurrido un error!','error');
        },
        success: function (data) {
            data = JSON.parse(data);

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: false,
            })

            if(!jQuery.isEmptyObject(data))
                Toast.fire({icon: data.icono, title: data.mensaje})
            else
                Toast.fire({icon: 'error', title: 'Error al Guardar!'})
        },
        complete: function () {
            $("#spinner").css('visibility', 'hidden');
        }
    });
});

init();