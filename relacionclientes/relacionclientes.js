var tabla;

var tabla_cxc;

//Función que se ejecuta al inicio
function init() {
    $("#loader").hide();
    $("#loader1").hide();
    $("#loader2").hide('');
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
                async: false,
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

                //lista de seleccion de ciudades
                $('#ciudad').append('<option name="" value="">Seleccione</option>');
                $.each(data.lista_ciudades, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#ciudad').append('<option name="" value="' + opt.ciudad +'">' + opt.descrip + '</option>');
                });

            }
        });
    });
});

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
            limpiar_modal_datos_cliente();
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

/*funcion para limpiar formulario de modal*/
function limpiar_modal_datos_cliente() {
    $('#tipoid3').val("").change();
    $('#cliente_form')[0].reset();
    $("#tipoid3").prop("disabled", false);
    $("#codclie").prop("readonly", false);
    $("#title_clienteModal").text("Agregar Cliente");
}

/*funcion para limpiar formulario de modal*/
function limpiar_modal_detalle_cliente() {
    $('#datos_ultimo_pago_y_venta').hide();
    $('#tabla_facturas_pendientes').hide();
    $("#descrip_cliente").text("");
    $("#codclient").text("");
    $("#descrip").text("");
    $("#edv").text("");
    $("#direccion").text("");
    $("#saldo").text("");
    $("#telefonos").text("");
    $("#limitecredito").text("");
    $("#diascredito").text("");
    $("#descuento").text("");
    $("#cod_documento_ultvent").text("");
    $("#MtoTotal_ultvent").text("");
    $("#fechae_ultvent").text("");
    $("#codusua_ultvent").text("");
    $("#cod_documento_ultpago").text("");
    $("#monto_ultpago").text("");
    $("#fechae_ultpago").text("");
    $("#codusua_ultpago").text("");
}

function limpiar_modal_detalle_factura() {
    $("#numero_factura").text("");
    $("#descrip_detfactura").text("");
    $("#codusua_detfactura").text("");
    $("#fechae_detfactura").text("");
    $("#codvend_detfactura").text("");
    $('#tabla_detalle_factura tbody').empty();
    $("#factura_despachada").html("");
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
        'columnDefs' : [{
            'visible': false, 'targets': [4]
        }],
        "iDisplayLength": 10,//Por cada 10 registros hace una paginación
        "order": [[4, "desc"]],//Ordenar (columna,orden)
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

function listar_cxc(codclie) {
    tabla_cxc = $('#relacion_facturas_pendientes').dataTable({

        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax": {
            url: 'relacionclientes_controlador.php?op=listar_facturas_pendientes',
            type: "post",
            dataType: "json",
            data: {codclie: codclie},
            error: function (e) {
                console.log(e.responseText);
            },
            complete: function () {
                //$("#tabla").show('');//MOSTRAMOS LA TABLA.
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
        }
    }).DataTable();
}

function mostrarModalDatosCliente(id_cliente = -1, tipoid3 = "") {

    limpiar_modal_datos_cliente();
    $('#clienteModal').modal('show');
    $("#loader1").show('');

    //si es -1 el modal es crear usuario nuevo
    if(id_cliente === -1)
    {
        $('#tipoid3').val("").change();
        var codclie = "";
        $.post("relacionclientes_controlador.php?op=listar_datos_cliente", { codclie: codclie }, function(data){
            data = JSON.parse(data);

            //lista de seleccion de estados
            $('#estado').append('<option name="" value="">Seleccione</option>');
            $.each(data.lista_estados, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#estado').append('<option name="" value="' + opt.estado +'">' + opt.descrip + '</option>');
            });

            //lista de seleccion de zonas
            $('#codzona').append('<option name="" value="">Seleccione</option>');
            $.each(data.lista_zonas, function(idx, opt) {
                $('#codzona').append('<option name="" value="' + opt.codzona +'">' + opt.descrip + '</option>');
            });

            //lista de seleccion de vendedores
            $('#codvend').append('<option name="" value="">Seleccione</option>');
            $.each(data.lista_vendedores, function(idx, opt) {
                $('#codvend').append('<option name="" value="' + opt.codvend +'">' + opt.descrip + '</option>');
            });

            //lista de seleccion de codigos nestle
            $('#codnestle').append('<option name="" value="">Seleccione</option>');
            $.each(data.lista_codnestle, function(idx, opt) {
                $('#codnestle').append('<option name="" value="' + opt.codnestle +'">' + opt.descrip + '</option>');
            });

            $("#loader1").hide();
        });
    } // si no es -1, el modal muestra los datos de un usuario por su id
    else if(id_cliente !== -1) {
        $("#title_clienteModal").text("Editar Cliente");
        $("#tipoid3").val(tipoid3);
        $("#tipoid3").prop("disabled", true);
        $.post("relacionclientes_controlador.php?op=listar_datos_cliente", { codclie: id_cliente }, function(data){
            data = JSON.parse(data);

            $("#tipoid3").change();

            //lista de seleccion de estados
            $('#estado').append('<option name="" value="">Seleccione</option>');
            $.each(data.lista_estados, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#estado').append('<option name="" value="' + opt.estado +'">' + opt.descrip + '</option>');
            });

            //lista de seleccion de zonas
            $('#codzona').append('<option name="" value="">Seleccione</option>');
            $.each(data.lista_zonas, function(idx, opt) {
                $('#codzona').append('<option name="" value="' + opt.codzona +'">' + opt.descrip + '</option>');
            });

            //lista de seleccion de vendedores
            $('#codvend').append('<option name="" value="">Seleccione</option>');
            $.each(data.lista_vendedores, function(idx, opt) {
                $('#codvend').append('<option name="" value="' + opt.codvend +'">' + opt.descrip + '</option>');
            });

            //lista de seleccion de codigos nestle
            $('#codnestle').append('<option name="" value="">Seleccione</option>');
            $.each(data.lista_codnestle, function(idx, opt) {
                $('#codnestle').append('<option name="" value="' + opt.codnestle +'">' + opt.descrip + '</option>');
            });

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

function mostrarModalDetalleCliente(codclie) {
    limpiar_modal_detalle_cliente();
    $('#detallecliente').modal('show');
    $("#loader2").show('');

    $.post("relacionclientes_controlador.php?op=detalle_de_cliente", {codclie: codclie}, function (data) {
        data = JSON.parse(data);

        $("#descrip_cliente").text(data.descrip);
        $("#codclient").text(codclie);
        $("#descrip").text(data.descrip);
        $("#edv").text(data.codvend);
        $("#direccion").text(data.direc1+" - "+data.direc2);
        $("#saldo").text(data.saldo+" Bs");
        $("#telefonos").text(data.telef+" -- "+data.movil);
        $("#limitecredito").text(data.LimiteCred+" Bs");
        $("#diascredito").text(data.diascred);
        $("#descuento").text(data.descto);

        if(data.visibilidad_datos_facturas.includes('true')){

            //mostrar los div
            $('#datos_ultimo_pago_y_venta').show();
            $('#tabla_facturas_pendientes').show();

            //datos ultima venta
            $("#cod_documento_ultvent").text(data.cod_documento_ultvent);
            $("#MtoTotal_ultvent").text(data.MtoTotal_ultvent+" Bs");
            $("#fechae_ultvent").text(data.fechae_ultvent);
            $("#codusua_ultvent").text(data.codusua_ultvent);

            //datos ultimo pago
            $("#cod_documento_ultpago").text(data.cod_documento_ultpago);
            $("#monto_ultpago").text(data.monto_ultpago+" Bs");
            $("#fechae_ultpago").text(data.fechae_ultpago);
            $("#codusua_ultpago").text(data.codusua_ultpago);

            //tabla de facturas pendientes
            listar_cxc(codclie);
        }

        $("#loader2").hide();
    });
}

function mostrarModalDetalleFactura(numerod, codclie) {
    limpiar_modal_detalle_factura();
    $('#detallefactura').modal('show');
    $("#loader3").show('');

    $.post("relacionclientes_controlador.php?op=detalle_de_factura", {numerod: numerod, codclie: codclie}, function (data) {
        data = JSON.parse(data);

        //cabecera de la factura
        $("#numero_factura").text(numerod);
        $("#descrip_detfactura").text(data.descrip);
        $("#codusua_detfactura").text(data.codusua);
        $("#fechae_detfactura").text(data.fechae);
        $("#codvend_detfactura").text(data.codvend);

        //detalle de la factura
        $.each(data.detalle_factura, function(idx, opt) {
            //como puede hacer varios registros de productos en una factura se itera con each
            $('#tabla_detalle_factura')
                .append(
                    '<tr>' +
                        '<td align="center" class="small align-middle">' + opt.coditem + '</td>' +
                        '<td align="center" class="small align-middle">' + opt.descrip1 + '</td>' +
                        '<td align="center" class="small align-middle">' + opt.cantidad + '</td>' +
                        '<td align="center" class="small align-middle">' + opt.tipounid + '</td>' +
                        '<td align="center" class="small align-middle">' + opt.totalitem + '</td>' +
                    '</tr>'
                );
        });

        $('#tabla_detalle_factura')
            .append( //separador
                '<tr>' +
                    '<td colspan="5">===================================================================</td>' +
                '</tr>'
            )
            .append( //totales de la factura
                '<tr>' +
                    '<td align="center" class="small align-middle"> Total de Bultos ' + data.bultos + '</td>' +
                    '<td colspan="2"></td>' +
                    '<td align="center" class="small align-middle"><div align="right">Sub Total</div></td>' +
                    '<td align="center" class="small align-middle"><div align="center">' + data.subtotal + '</div></td>' +
                '</tr>' +
                '<tr>' +
                    '<td align="center" class="small align-middle"> Total de Paquetes ' + data.paquetes + '</td>' +
                    '<td colspan="2"></td>' +
                    '<td align="center" class="small align-middle"><div align="right">Descuento</div></td>' +
                    '<td align="center" class="small align-middle"><div align="center">' + data.descuento + '</div></td>' +
                '</tr>' +
                '<tr>' +
                    '<td colspan="3"></td>' +
                    '<td align="center" class="small align-middle"><div align="right">Excento</div></td>' +
                    '<td align="center" class="small align-middle"><div align="center">' + data.exento + '</div></td>' +
                '</tr>' +

                '<tr>' +
                    '<td colspan="3"></td>' +
                    '<td align="center" class="small align-middle"><div align="right">Base Imponible</div></td>' +
                    '<td align="center" class="small align-middle"><div align="center">' + data.base + '</div></td>' +
                '</tr>' +
                '<tr>' +
                    '<td colspan="3"></td>' +
                    '<td align="center" class="small align-middle"><div align="right">Impuestos ' + data.iva + ' %</div></td>' +
                    '<td align="center" class="small align-middle"><div align="center">' + data.impuesto + '</div></td>' +
                '</tr>' +
                '<tr>' +
                    '<td colspan="3"></td>' +
                    '<td align="center" class="small align-middle"><div align="right">Monto Total</div></td>' +
                    '<td align="center" class="small align-middle"><div align="center">' + data.total + '</div></td>' +
                '</tr>'
            );
        $("#factura_despachada").html(data.factura_despachada);

        $("#loader3").hide();
    });
}
init();