var tabla_clientesnoactivos;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    $("#loader").hide();
    estado_minimizado = false;
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
    $("#vendedor").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla_clientesnoactivos.rows().count() === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function()
{
    ($("#fechai").val() !== "" && $("#fechaf").val() !== "" && $("#vendedor").val() !== "")
        ? estado_minimizado = true : estado_minimizado = false ;
};

$(document).ready(function(){
    $("#fechai").change( () => no_puede_estar_vacio() );
    $("#fechaf").change( () => no_puede_estar_vacio() );
    $("#vendedor").change( () => no_puede_estar_vacio() );
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_clientesnoactivos", function () {

    var fechai = $("#fechai").val();
    var fechaf = $("#fechaf").val();
    var vendedor = $("#vendedor").val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (fechai !== "" && fechaf !== "" && vendedor !== "") {
            sessionStorage.setItem("fechai", fechai);
            sessionStorage.setItem("fechaf", fechaf);
            sessionStorage.setItem("vendedor", vendedor);
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla_clientesnoactivos = $('#clientesnoactivos_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    beforeSend: function () {
                        $("#loader").show(''); //MOSTRAMOS EL LOADER.
                    },
                    url: "clientesnoactivos_controlador.php?op=buscar_clientesnoactivos",
                    type: "post",
                    data: {fechai: fechai, fechaf: fechaf, vendedor: vendedor},
                    error: function (e) {
                        console.log(e.responseText);
                    },
                    complete: function () {

                        $("#tabla").show('');//MOSTRAMOS LA TABLA.
                        $("#loader").hide();//OCULTAMOS EL LOADER.
                        validarCantidadRegistrosTabla();
                        mostrar()
                        limpiar();//LIMPIAMOS EL SELECTOR.

                    }
                },//TRADUCCION DEL DATATABLE.
                "bDestroy": true,
                "responsive": true,
                "bInfo": true,
                "iDisplayLength": 10,
                "order": [[0, "desc"]],
                "language": {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ningún dato disponible en esta tabla",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
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
                },
            });
            estado_minimizado = true;
        }
    } else {
        Swal.fire('Atención!', 'Debe seleccionar un rango de fecha y un vendedor.', 'error');
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
   var fechai = sessionStorage.getItem("fechai", fechai);
   var fechaf = sessionStorage.getItem("fechaf", fechaf);
   var vendedor = sessionStorage.getItem("vendedor", vendedor);
   if (fechai !== "" && fechaf !== "" && vendedor !== "") {
    window.location = "clientesnoactivos_excel.php?&fechai="+fechai+"&fechaf="+fechaf+"&vendedor="+vendedor;
}
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    var vendedor = sessionStorage.getItem("vendedor", vendedor);
    if (fechai !== "" && fechaf !== "" && vendedor !== "") {
        window.open('clientesnoactivos_pdf.php?&fechai='+fechai+'&fechaf='+fechaf+'&vendedor='+vendedor, '_blank');
    }
});

function mostrar() {
   /* var fechai = $("#fechai").val();
    var fechaf = $("#fechaf").val();
    var vendedor = $("#vendedor").val();
    $.post("clientesnoactivos_controlador.php?op=mostrar", {fechai: fechai, fechaf: fechaf, vendedor: vendedor}, function (data, status) {
        data = JSON.parse(data);

        $("#cuenta").html(data.cuenta);

    });*/

    var texto= 'Clientes No Activados: ';
    var cuenta =(tabla_clientesnoactivos.rows().count());
    $("#cuenta").html(texto + cuenta);
}

init();
