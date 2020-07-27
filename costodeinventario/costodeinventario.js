var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    $("#loader").hide();
    estado_minimizado = false;
}

function limpiar() {
    $("#marca").val("");
    $("#depo").val("");
    $('#tabla tbody').empty();
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
        $("#minimizar").slideToggle();
        estado_minimizado = false;
        if (marcas !== "") {
            var datos=$('#frmCostos').serialize();
            // var formData = new FormData($("#cliente_form")[0]);
            limpiar();//LIMPIAMOS EL SELECTOR.
            $.ajax({
                beforeSend: function () {
                    $("#loader").show();
                },
                type: "POST",
                url: "costodeinventario_controlador.php?op=buscar_costoseinventarios",
                data: datos,
                error: function(X){
                    Swal.fire('Atención!','ha ocurrido un error!','error');
                },
                success: function(data) {
                    data = JSON.parse(data);
                    $("#loader").hide();
                    imprimir_tabla(data);
                    // $("#costos_inv_ver").html(response);
                }
            });
            estado_minimizado = true;
        }
    } else {
        Swal.fire('Atención!','Seleccione una Marca!','error');
        return (false);

    }
    /*if (marcas === "") {
        Swal.fire('Atención!','Seleccione una Marca!','error');
        return (false);
    }else {
        var datos=$('#frmCostos').serialize();
        $.ajax({
            beforeSend: function () {
                $("#loader").show();
            },
            type: "POST",
            url: "costodeinventario_controlador.php",
            data: datos,
            error: function(X){
                Swal.fire('Atención!','ha ocurrido un error!','error');
            },
            success: function(response) {
                $("#tabla").show('');//MOSTRAMOS LA TABLA.
                $("#minimizar").slideToggle();
                $("#loader").hide();
                $("#costos_inv_ver").html(response);
                limpiar();//LIMPIAMOS EL SELECTOR.
            }
        });
    }*/
});

function imprimir_tabla(data) {
    if(!jQuery.isEmptyObject(data.contenido_tabla)){

        //detalle de la factura
        $.each(data.contenido_tabla, function(idx, opt) {
            //como puede hacer varios registros de productos en una factura se itera con each
            $('#tabla')
                .append(
                    '<tr>' +
                    '<td>' + opt.codprod + '</td>' +
                    '<td>' + opt.descrip1 + '</td>' +
                    '<td>' + opt.cantidad + '</td>' +
                    '<td>' + opt.tipounid + '</td>' +
                    '<td>' + opt.totalitem + '</td>' +
                    '</tr>'
                );
        });

    }
}

function f() {
    //lista de seleccion de vendedores
    $('#vendedor').append('<option name="" value="">Seleccione</option>');
    $.each(data.lista_vendedores, function(idx, opt) {
        //se itera con each para llenar el select en la vista
        $('#vendedor').append('<option name="" value="' + opt.CodVend +'">' + opt.CodVend + ': ' + opt.Descrip.substr(0, 35) + '</option>');
    });
}

/*
//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    var marca = sessionStorage.getItem("marca", marca);
    if (fechai !== "" && fechaf !== "" && marca !== "") {
        window.open('sellin_pdf.php?&fechai='+fechai+'&fechaf='+fechaf+'&marca='+marca, '_blank');
    }
});
*/

init();
