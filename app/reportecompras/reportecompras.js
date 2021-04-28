var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    $("#loader").hide();
    estado_minimizado = false;
    listar_marcas();
}

function limpiar() {
    $("#fechai").val("");
    $("#marca").val("");
    $('#total_items').show();
    $('#total_registros').text("");
    $('#tabla tbody').empty();
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
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

var no_puede_estar_vacio = function () {
    ($("#fechai").val().length > 0  &&  $("#marca").val().length > 0) ? estado_minimizado = true : estado_minimizado = false;
};

$(document).ready(function () {
    $("#fechai").change(() => no_puede_estar_vacio());
    $("#marca").change(() => no_puede_estar_vacio());
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_reportecompra", function () {

    var fechai = $("#fechai").val();
    var marca = $("#marca").val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (fechai !== "" && marca !== "") {
            sessionStorage.setItem("fechai", fechai);
            sessionStorage.setItem("marca", marca);
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.

            $.ajax({
                beforeSend: function () {
                    $("#loader").show();
                },
                type: "POST",
                url: "reportecompras_controlador.php?op=listar",
                data: {fechai: fechai, marca: marca},
                error: function(X){
                    Swal.fire('Atención!','ha ocurrido un error!','error');
                },
                success: function(data) {
                    data = JSON.parse(data);
                    $("#tabla").show();
                    $("#loader").hide();
                    limpiar();//LIMPIAMOS EL SELECTOR.
                    imprimir_tabla(data);
                }
            });
            estado_minimizado = true;
        }
    } else {
        Swal.fire('Atención!', 'Debe seleccionar fecha y marca valido!', 'error');
        return (false);
    }
});

function imprimir_tabla(data) {
    if(!jQuery.isEmptyObject(data.contenido_tabla)){

        //contenido de costos e inventario se itera con el comando $.each
        $.each(data.contenido_tabla, function(idx, opt) {
            //se va llenando cada registo en el tbody
            var bg_alert = (parseInt(opt.rentabilidad) >= 30) ? "#ff3939" : ""
            if(opt.rentabilidad > 30)
                console.log(bg_alert)

            $('#reportecompras_data')
                .append(
                    '<tr>' +
                    '<td>' + opt.num + '</td>' +
                    '<td>' + opt.codproducto + '</td>' +
                    '<td>' + opt.descrip + '</td>' +
                    '<td>' + opt.displaybultos + '</td>' +
                    '<td>' + opt.costodisplay + '</td>' +
                    '<td>' + opt.costobultos + '</td>' +
                    '<td BGCOLOR="'+bg_alert+'" >' + opt.rentabilidad + '%</td>' +
                    '<td>' + opt.fechapenultimacompra + '</td>' +
                    '<td>' + opt.bultospenultimacompra + '</td>' +
                    '<td>' + opt.fechaultimacompra + '</td>' +
                    '<td>' + opt.bultosultimacompra + '</td>' +
                    '<td>' + opt.semana1 + '</td>' +
                    '<td>' + opt.semana2 + '</td>' +
                    '<td>' + opt.semana3 + '</td>' +
                    '<td>' + opt.semana4 + '</td>' +
                    '<td>' + opt.totalventasmesanterior + '</td>' +
                    '<td>' + opt.bultosexistentes + '</td>' +
                    '<td>' + opt.diasdeinventario + '</td>' +
                    '<td>' + opt.sugerido + '</td>' +
                    '<td>' + opt.pedido + '</td>' +
                    '</tr>'
                );
        });

        //se asigna la totalidad de los registros
        $('#total_registros').text(data.cantidad_registros);
    } else {
        //en caso de consulta vacia, mostramos un mensaje de vacio
        $('#reportecompras_data').append('<tr><td colspan="20" align="center">Sin registros para esta Consulta</td></tr>');
        //inhabilitamos visualmente los botones
        $('#btn_excel').attr("disabled", true);
        $('#btn_pdf').attr("disabled", true);
        $('#total_items').hide();
    }
}

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var fechai = sessionStorage.getItem("fechai");
    var marca = sessionStorage.getItem("marca");
    var datos=$('#form_reportecompras').serialize();
    if (fechai !== "" && marca !== "") {
        window.location = "reportecompras_excel.php?&fechai="+fechai+"&marca="+marca+"&"+datos;
        // window.open('reportecompras_excel.php?&fechai='+fechai+'&marca='+marca+'&'+datos, '_blank');
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai");
    var marca = sessionStorage.getItem("marca");
    var datos=$('#form_reportecompras').serialize();
    if (fechai !== "" && marca !== "") {
        window.open('reportecompras_pdf.php?&fechai='+fechai+'&marca='+marca+'&'+datos, '_blank');
    }
});

init();