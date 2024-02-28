var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;
    //listar_marcas();
    listar_vendedores();
}

function limpiar() {
    $("#marca").val("");
    $('[name="depo[]"]').val("").trigger("change");
    $("#marca").attr("disabled", false);
    $('[name="depo[]"]').attr("disabled", false);
    $('#total_items').show();
    $('#total_registros').text("");
    $('#tabla tbody').empty();
    $('#btn_excel').attr("disabled", false);
    $('#btn_pdf').attr("disabled", false);

}

var no_puede_estar_vacio = function () {
    ($('[name="depo[]"]').val() !== "") ? estado_minimizado = true : estado_minimizado = false;
};

$(document).ready(function () {
    $('[name="depo[]"]').change(() => no_puede_estar_vacio());
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {

 //   var marcas = $('#marca').val();
    var datos = $('#frmCostos').serialize();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();
        estado_minimizado = false;
        if (datos) {
            //serializamos la informacion de marca y almacen
            
            let isError = false;
            //almacenamos en sesion una variable
            sessionStorage.setItem("datos", datos);
            $.ajax({
                url: "NEcobros_controlador.php?op=listar",
                method: "POST",
                dataType: "json",
                data: datos,
                beforeSend: function () {
                    SweetAlertLoadingShow();
                },
                error: function (e) {
                   // isError = SweetAlertError(e.responseText, "Error!")
                    send_notification_error(e.responseText);
                    console.log(e.responseText);
                    limpiar();
                },
                success: function(data) {
                    limpiar();//LIMPIAMOS EL SELECTOR.
                    imprimir_tabla(data);
                },
                complete: function () {
                    if(!isError) SweetAlertLoadingClose();
                    $("#tabla").show('');//MOSTRAMOS LA TABLA.
                }
            });
            estado_minimizado = true;
       }
    } else {
        SweetAlertError('Seleccione un Vendedor!');
        return (false);

    }
});

function imprimir_tabla(data) {
    if(!jQuery.isEmptyObject(data.contenido_tabla)){

        //contenido de costos e inventario se itera con el comando $.each
        $.each(data.contenido_tabla, function(idx, opt) {
            //se va llenando cada registo en el tbody
            $('#NEcobros_data')
                .append(
                    '<tr>' +
                    '<td>' + opt.ruta + '</td>' +
                    '<td>' + opt.NroDoc + '</td>' +
                    '<td>' + opt.CodClie + '</td>' +
                    '<td>' + opt.Cliente + '</td>' +
                    '<td>' + opt.FechaEmi + '</td>' +
                    '<td>' + opt.p0_7 + '</td>' +
                    '<td>' + opt.p8_15 + '</td>' +
                    '<td>' + opt.p16_40 + '</td>' +
                    '<td>' + opt.mas_40 + '</td>' +
                    '<td>' + opt.SaldoPend + '</td>' +
                    '<td>' + opt.Supervisor + '</td>' +
                    '</tr>'
                );
        });

        //al final se agrega un registro mas que son los totales
        //se va llenando cada registo en el tbody
        $('#NEcobros_data')
            .append(
                '<tr>' +
                    '<td colspan="5" align="right"><strong>Totales: </strong></td>' +
                '<td><strong>' + data.totales_tabla.SaldoPend_07 + '</strong></td>' +
                '<td><strong>' + data.totales_tabla.SaldoPend_815 + '</strong></td>' +
                '<td><strong>' + data.totales_tabla.SaldoPend_164 + '</strong></td>' +
                '<td><strong>' + data.totales_tabla.SaldoPend_m40 + '</strong></td>' +
                '<td><strong>' + data.totales_tabla.SaldoPend + '</strong></td>' +
                '<td>  </td>' +
                '</tr>'
            );

        //se asigna la totalidad de los registros
        $('#total_registros').text(data.totales_tabla.cantidad_registros);
    } else {
        //en caso de consulta vacia, mostramos un mensaje de vacio
        $('#NEcobros_data').append('<tr><td colspan="11" align="center">Sin registros para esta Consulta</td></tr>');
        //inhabilitamos visualmente los botones
        $('#btn_excel').attr("disabled", true);
        $('#btn_pdf').attr("disabled", true);
        $('#total_items').hide();
    }
}

function listar_marcas() {
    let isError = false;
    $.ajax({
        url: "NEcobros_controlador.php?op=listar_marcas",
        type: "get",
        dataType: "json",
        beforeSend: function () {
            SweetAlertLoadingShow();
            //mientras carga inabilitamos el select
            $("#marca").attr("disabled", true);
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function(data) {
            //cuando termina la consulta, activamos el boton
            $("#marca").attr("disabled", false);
            //lista de seleccion de las marcas
            $('#marca')
                .append('<option name="" value="">Seleccione una Marca</option>')
                .append('<option name="" value="-">TODAS</option>');
            $.each(data.lista_marcas, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#marca').append('<option name="" value="' + opt.marca +'">' + opt.marca + '</option>');
            });
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

function listar_vendedores() {
    $.ajax({
        url: "NEcobros_controlador.php?op=listar_vendedores",
        type: "get",
        dataType: "json",
        beforeSend: function () {
            //mientras carga inabilitamos el select
            $('[name="depo[]"]').attr("disabled", true);
        },
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function(data) {
            //cuando termina la consulta, activamos el boton
            $('[name="depo[]"]').attr("disabled", false);
            //lista de seleccion de depositos
            $('[name="depo[]"]').append('<option name="" value="TODOS">Todos</option>');
            $.each(data.lista_vendedores, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('[name="depo[]"]').append('<option name="" value="' + opt.CodVend + '">' + opt.CodVend + ': ' + opt.Descrip.substr(0, 35) + '</option>');
            });
        }
    });
}

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var datos = sessionStorage.getItem("datos");
    if (datos !== "") {
        window.location = 'NEcobros_excel.php?&'+datos;
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var datos = sessionStorage.getItem("datos");
    if (datos !== "") {
        window.open('costodeinventario_pdf.php?&'+datos, '_blank');
    }
});

init();
