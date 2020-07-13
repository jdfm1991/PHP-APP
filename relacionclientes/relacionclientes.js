var tabla;

//Función que se ejecuta al inicio
function init() {
    $("#loader").hide();
    $("#loader1").hide();
    listar();
    //cuando se da click al boton submit entonces se ejecuta la funcion guardaryeditar(e);
    $("#btnGuardarUsuario").on("click", function (e) {
        guardaryeditar(e);
    });

}

$(document).ready(function () {
    //VALIDA CADA INPUT CUANDO ES CAMBIADO DE ESTADO
    $("#tipoid3").change(function(){
        $('#cliente_form')[0].reset();
        $('#tipo_cliente').val($("#tipoid3").val());

        if($("#tipoid3").val() !== '')
        {
            $('#cliente_form').show();
            $.ajax({
                url: "relacionclientes_controlador.php?op=obtener_opcion_para_juridico_o_natural",
                method: "POST",
                data: { tipo: $("#tipoid3").val() },
                error: function (e) {
                    console.log(e.responseText);
                },
                success: function (data) {
                    data = JSON.parse(data);
                    $("#div_descrip").html(data.descrip);
                    $("#div_ruc").html(data.ruc);
                    $("#codclie").attr("placeholder", data.codclie);
                    $("#id3").attr("placeholder", data.rif);
                }
            });
            $("#btnGuardarUsuario").prop("disabled", false);
        } else {
            $('#cliente_form').hide();
            $("#btnGuardarUsuario").prop("disabled", true);
        }
    });

    $("#estado").change(function(){
        $.ajax({
            async: false,
            cache: true,
            url: "relacionclientes_controlador.php?op=listar_ciudades_por_idestado",
            method: "POST",
            data: { idestado: $("#estado").val() },
            error: function (e) {
                console.log(e.responseText);
            },
            success: function (data) {
                data = JSON.parse(data);
                $("#ciudad").html(data.ciudades);
            }
        });
    });
    /*$("#chofer").change(() => no_puede_estar_vacio());
    $("#vehiculo").change(() => { no_puede_estar_vacio();
        cargarCapacidadVehiculo($("#vehiculo").val());
    });
    $("#destino").on('keyup', () => no_puede_estar_vacio()).keyup();
    $("#factura").on('keyup', () => no_puede_estar_vacio()).keyup();*/

});

/*funcion para limpiar formulario de modal*/
function limpiar() {
    $('#tipoid3').val("").change();
    $('#cliente_form')[0].reset();
    $("#tipoid3").prop("disabled", false);
    $("#codclie").prop("readonly", false);
}

//function listar
function listar() {
    tabla = $('#cliente_data').dataTable({

        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax": {
            beforeSend: function () {
                $("#loader").show(''); //MOSTRAMOS EL LOADER.
            },
            url: 'relacionclientes_controlador.php?op=listar',
            type: "get",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            },
            complete: function () {
                $("#tabla").show('');//MOSTRAMOS LA TABLA.
                $("#loader").hide();//OCULTAMOS EL LOADER.
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

function mostrar(id_cliente = -1, tipoid3 = "") {

    limpiar();
    $('#clienteModal').modal('show');
    $("#loader1").show('');

    //si es -1 el modal es crear usuario nuevo
    if(id_cliente === -1)
    {
        $('#tipoid3').val("").change();
        var codclie = "";
        $.post("relacionclientes_controlador.php?op=listar_detalle_cliente", { codclie: codclie }, function(data){
            data = JSON.parse(data);
            $("#estado").html(data.estado);
            $("#codzona").html(data.zona);
            $("#codvend").html(data.edv);
            $("#codnestle").html(data.codnestle);

            $("#loader1").hide();
        });
    } // si no es -1, el modal muestra los datos de un usuario por su id
    else if(id_cliente !== -1) {
        $('.modal-title').text("Editar Cliente");
        $("#tipoid3").val(tipoid3).change();
        $("#tipoid3").prop("disabled", true);
        $.post("relacionclientes_controlador.php?op=listar_detalle_cliente", { codclie: id_cliente }, function(data){
            data = JSON.parse(data);
            $("#estado").html(data.estado);
            $("#codzona").html(data.zona);
            $("#codvend").html(data.edv);
            $("#codnestle").html(data.codnestle);

            $("#id_cliente").val(id_cliente);
            $("#codclie").val(data.codclie);
            $("#codclie").prop("readonly", true);
            if(tipoid3 === "0"){
                $("#descrip").val(data.descrip);
                $("#ruc").val(data.ruc);
            } else
                if(tipoid3 === "1"){
                    $("#name1").val(data.name1);
                    $("#name2").val(data.name2);
                    $("#ape1").val(data.ape1);
                    $("#ape2").val(data.ape2);
            }
            $("#id3").val(data.id3);
            $("#clase").val(data.clase);
            $("#represent").val(data.represent);
            $("#direc1").val(data.direc1);
            $("#direc2").val(data.direc2);
            $("#estado").val(data.idestado).change();
            $("#ciudad").val(data.idciudad);
            $("#municipio").val(data.municipio);
            $("#email").val(data.email);
            $("#telef").val(data.telef);
            $("#movil").val(data.movil);
            $("#activo").val(data.idactivo);
            $("#codzona").val(data.codzona);
            $("#codvend").val(data.codvend);
            $("#tipocli").val(data.tipocli);
            $("#tipopvp").val(data.idtpvp);
            $("#diasvisita").val(data.diasvisita);
            $("#latitud").val(data.latitud);
            $("#longitud").val(data.longitud);
            $("#codnestle").val(data.idnestle);
            $("#escredito").val(data.escredito);
            $("#LimiteCred").val(data.LimiteCred);
            $("#diascred").val(data.diascred);
            $("#estoleran").val(data.estoleran);
            $("#diasTole").val(data.diasTole);
            $("#descto").val(data.descto);
            $("#observa").val(data.observa);

            $("#loader1").hide();
        });
    }
}



//la funcion guardaryeditar(e); se llama cuando se da click al boton submit
function guardaryeditar(e) {
    var cliente = $('#codclie').val();
    console.log("guardar y editar "+cliente);
    // return;

    e.preventDefault(); //No se activará la acción predeterminada del evento
    var formData = new FormData($("#cliente_form")[0]);

    $.ajax({
        url: "relacionclientes_controlador.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        error: function (e) {
            console.log(e.responseText);
        },
        success: function (data) {
            data = JSON.parse(data);
            console.log(data);
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            })
            Toast.fire({
                icon: data.icono,
                title: data.mensaje
            })
            limpiar();
            $('#clienteModal').modal('hide');

            // si no hubo error, recarga la tabla
            if(!data.icono.includes('error')){
                $('#cliente_data').DataTable().ajax.reload();
            }

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
                url: "relacionclientes_controlador.php?op=activarydesactivar",
                method: "POST",
                data: {codclie: id, est: est},
                error: function (e) {
                    console.log(e.responseText);
                },
                success: function (data) {
                    $('#cliente_data').DataTable().ajax.reload();
                }
            });
        }
    })
}

init();