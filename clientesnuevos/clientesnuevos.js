var tabla_clientesnuevos;

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
}

$(document).ready(function(){
    $("#fechai").change( () => estado_minimizado = true )
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_clientesnuevos", function () {

    var fechai = $("#fechai").val();
    var fechaf = $("#fechaf").val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (fechai != "" && fechaf != "") {
            sessionStorage.setItem("fechai", fechai);
            sessionStorage.setItem("fechaf", fechaf);
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla_clientesnuevos = $('#clientesnuevos_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    beforeSend: function () {
                        $("#loader").show(''); //MOSTRAMOS EL LOADER.
                    },
                    url: "clientesnuevos_controlador.php?op=buscar_clientesnuevos",
                    type: "post",
                    data: {fechai: fechai, fechaf: fechaf},
                    error: function (e) {
                        console.log(e.responseText);
                    },
                    complete: function () {

                        $("#tabla").show('');//MOSTRAMOS LA TABLA.
                        $("#loader").hide();//OCULTAMOS EL LOADER.
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
        Swal.fire('Atención!', 'Debe seleccionar un rango de Fecha!', 'error');
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){

   var fechai = sessionStorage.setItem("fechai", fechai);
   var fechaf = sessionStorage.setItem("fechaf", fechaf);
   if (vendedor !== "") {
    window.location = "clientesnuevos_excel.php?vendedor="+vendedor;
}
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var vendedor = sessionStorage.getItem("vendedor");
    if (vendedor !== "") {
        window.open('clientesnuevos_pdf.php?&vendedor='+vendedor, '_blank');
    }
});

function mostrar() {
    var fechai = $("#fechai").val();
    var fechaf = $("#fechaf").val();
    $.post("clientesnuevos_controlador.php?op=mostrar", {fechai: fechai, fechaf: fechaf}, function (data, status) {
        data = JSON.parse(data);

        $("#cuenta").html(data.cuenta);

    });
}

init();
