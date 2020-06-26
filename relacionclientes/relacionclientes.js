var tabla;

//Función que se ejecuta al inicio
function init() {
    listar();

    //cuando se da click al boton submit entonces se ejecuta la funcion guardaryeditar(e);
    $("#btnGuardarUsuario").on("click", function (e) {
        guardaryeditar(e);
    });

    //cambia el titulo de la ventana modal cuando se da click al boton
    /*$("#add_button").click(function () {

        //habilita los campos cuando se agrega un registro nuevo ya que cuando se editaba un registro asociado entonces aparecia deshabilitado los campos
        $("#cedula").attr('disabled', false);
        $("#login").attr('disabled', false);
        $("#nomper").attr('disabled', false);

        $(".modal-title").text("Agregar Usuario");

    });*/
}

/*funcion para limpiar formulario de modal*/
function limpiar() {
    $('#tipoid3').val("").change();
    $('#cliente_form')[0].reset();
}

//function listar
function listar() {
    tabla = $('#cliente_data').dataTable({

        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax":
            {
                url: 'relacionclientes_controlador.php?op=listar',
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
        "order": [[0, "asc"]],//Ordenar (columna,orden)
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

$(document).ready(function () {
    //VALIDA CADA INPUT CUANDO ES CAMBIADO DE ESTADO
    $("#tipoid3").change(function(){
        $('#cliente_form')[0].reset();
        $('#tipo_cliente').val($("#tipoid3").val());

        if($("#tipoid3").val() !== '')
        {
            $('#cliente_form').show();
            $.post("relacionclientes_controlador.php?op=obtener_opcion_para_juridico_o_natural", { tipo: $("#tipoid3").val() }, function(data){
                data = JSON.parse(data);
                $("#descrip").html(data.descrip);
                $("#ruc").html(data.ruc);
                $("#codclie").attr("placeholder", data.codclie);
                $("#id3").attr("placeholder", data.rif);
            });
            $("#btnGuardarUsuario").prop("disabled", false);
        } else {
            $('#cliente_form').hide();
            $("#btnGuardarUsuario").prop("disabled", true);
        }
    });

    /*$("#chofer").change(() => no_puede_estar_vacio());
    $("#vehiculo").change(() => { no_puede_estar_vacio();
        cargarCapacidadVehiculo($("#vehiculo").val());
    });
    $("#destino").on('keyup', () => no_puede_estar_vacio()).keyup();
    $("#factura").on('keyup', () => no_puede_estar_vacio()).keyup();*/

});

function mostrar(id_usuario = -1) {

    // $('#tipoid3').val("").change();

    //si es -1 el modal es crear usuario nuevo
    if(id_usuario === -1)
    {
        $('#tipoid3').val("").change();
        $('#clienteModal').modal('show');
    } // si no es -1, el modal muestra los datos de un usuario por su id
    else if(id_usuario !== -1) {
        $.post("usuario_controlador.php?op=mostrar", {id_usuario: id_usuario}, function (data, status) {
            data = JSON.parse(data);

            if (data.cedula_relacion) {

                $('#tipoid3').val("").change();
                $('#clienteNuevoModal').modal('show');

                $('#cedula').val(data.cedula_relacion);


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

            } else {

                $('#usuarioModal').modal('show');
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
            }
        });
    }
}



//la funcion guardaryeditar(e); se llama cuando se da click al boton submit
function guardaryeditar(e) {
    var tipo_cliente = parseInt($('#tipoid3').val());
    console.log("guardar y editar");
    return;

    e.preventDefault(); //No se activará la acción predeterminada del evento
    var formData = new FormData($("#cliente_form")[0]);

    $.ajax({
        url: "relacionclientes_controlador.php?op=guardaryeditar",
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
                url: "usuario_controlador.php?op=activarydesactivar",
                method: "POST",
                data: {id: id, est: est},
                success: function (data) {
                    $('#usuario_data').DataTable().ajax.reload();
                }
            });
        }
    })
}

init();