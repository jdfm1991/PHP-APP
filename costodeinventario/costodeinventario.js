var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    $("#loader").hide();
    estado_minimizado = false;
    listar_marcas();
    listar_almacenes();
}

function limpiar() {
    $("#marca").val("");
    $("#depo").val("");
    $("#marca").attr("disabled", false);
    $('[name="depo[]"]').attr("disabled", false);
    $('#total_items').show();
    $('#total_registros').text("");
    $('#tabla tbody').empty();
    $('#btn_excel').attr("disabled", false);
    $('#btn_pdf').attr("disabled", false);

}

var no_puede_estar_vacio = function () {
    ($("#marca").val() !== "") ? estado_minimizado = true : estado_minimizado = false;
};

$(document).ready(function () {
    $("#marca").change(() => no_puede_estar_vacio());
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_costodeinventario", function () {

    var marcas = $('#marca').val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#loader").hide();
        $("#minimizar").slideToggle();
        estado_minimizado = false;
        if (marcas !== "") {
            //serializamos la informacion de marca y almacen
            var datos=$('#frmCostos').serialize();
            //almacenamos en sesion una variable
            sessionStorage.setItem("datos", datos);
            limpiar();//LIMPIAMOS EL SELECTOR.
            $.ajax({
                beforeSend: function () {
                    $("#loader").show();
                },
                type: "POST",
                url: "costodeinventario_controlador.php?op=listar_costoseinventario",
                data: datos,
                error: function(X){
                    Swal.fire('Atención!','ha ocurrido un error!','error');
                },
                success: function(data) {
                    data = JSON.parse(data);
                    $("#tabla").show();
                    $("#loader").hide();
                    imprimir_tabla(data);
                }
            });
            estado_minimizado = true;
        }
    } else {
        Swal.fire('Atención!','Seleccione una Marca!','error');
        return (false);

    }
});

function imprimir_tabla(data) {
    if(!jQuery.isEmptyObject(data.contenido_tabla)){

        //contenido de costos e inventario se itera con el comando $.each
        $.each(data.contenido_tabla, function(idx, opt) {
            //se va llenando cada registo en el tbody
            $('#costodeinventario_data')
                .append(
                    '<tr>' +
                        '<td>' + opt.codprod + '</td>' +
                        '<td>' + opt.descrip + '</td>' +
                        '<td>' + opt.marca + '</td>' +
                        '<td>' + opt.costo + '</td>' +
                        '<td>' + opt.cdisplay + '</td>' +
                        '<td>' + opt.precio + '</td>' +
                        '<td>' + opt.bultos + '</td>' +
                        '<td>' + opt.paquetes + '</td>' +
                        '<td>' + opt.costoxbulto + '</td>' +
                        '<td>' + opt.cdisplayxpaquetes + '</td>' +
                        '<td>' + opt.tara + '</td>' +
                    '</tr>'
                );
        });

        //al final se agrega un registro mas que son los totales
        //se va llenando cada registo en el tbody
        $('#costodeinventario_data')
            .append(
                '<tr>' +
                    '<td colspan="3" align="right"><strong>Totales: </strong></td>' +
                    '<td>' + data.totales_tabla.costos + '</td>' +
                    '<td>' + data.totales_tabla.costos_p + '</td>' +
                    '<td>' + data.totales_tabla.precios + '</td>' +
                    '<td>' + data.totales_tabla.bultos + '</td>' +
                    '<td>' + data.totales_tabla.paquetes + '</td>' +
                    '<td>' + data.totales_tabla.total_costo_bultos + '</td>' +
                    '<td>' + data.totales_tabla.total_costo_paquetes + '</td>' +
                    '<td>' + data.totales_tabla.total_tara + '</td>' +
                '</tr>'
            );

        //se asigna la totalidad de los registros
        $('#total_registros').text(data.totales_tabla.cantidad_registros);
    } else {
        //en caso de consulta vacia, mostramos un mensaje de vacio
        $('#costodeinventario_data').append('<tr><td colspan="11" align="center">Sin registros para esta Consulta</td></tr>');
        //inhabilitamos visualmente los botones
        $('#btn_excel').attr("disabled", true);
        $('#btn_pdf').attr("disabled", true);
        $('#total_items').hide();
    }
}

function listar_marcas() {
    $.ajax({
        url: "../sellin/sellin_controlador.php?op=listar_marcas",
        type: "GET",
        beforeSend: function () {
            //mientras carga inabilitamos el select
            $("#marca").attr("disabled", true);
        },
        error: function(X){
            Swal.fire('Atención!','ha ocurrido un error!','error');
        },
        success: function(data) {
            data = JSON.parse(data);
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
        }
    });
}

function listar_almacenes() {
    $.ajax({
        url: "costodeinventario_controlador.php?op=listar_depositos",
        type: "GET",
        beforeSend: function () {
            //mientras carga inabilitamos el select
            $('[name="depo[]"]').attr("disabled", true);
        },
        error: function(X){
            Swal.fire('Atención!','ha ocurrido un error!','error');
        },
        success: function(data) {
            data = JSON.parse(data);
            //cuando termina la consulta, activamos el boton
            $('[name="depo[]"]').attr("disabled", false);
            //lista de seleccion de depositos
            $.each(data.lista_depositos, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('[name="depo[]"]').append('<option name="" value="' + opt.codubi +'">' + opt.codubi + ': '+ opt.descrip.substr(0, 35) + '</option>');
            });
        }
    });
}

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var datos = sessionStorage.getItem("datos");
    if (datos !== "") {
        window.open('costodeinventario_excel.php?&'+datos, '_blank');
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
