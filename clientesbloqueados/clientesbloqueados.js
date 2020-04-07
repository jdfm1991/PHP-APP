var tabla_clientesbloqueados;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    $("#loader").hide();
}

function limpiar() {
    $("#vendedor").val("");
}

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_clientesbloqueados", function () {
    var vendedor = $("#vendedor").val();
    if (vendedor === "") {
        Swal.fire('Atención!', 'Debe seleccionar al menos un Vendedor!', 'error');
        return (false);
    } else {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        sessionStorage.setItem("vendedor", vendedor);
        //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
        tabla_clientesbloqueados = $('#clientesbloqueados_data').DataTable({
            "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
            "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
            "ajax": {
                beforeSend: function () {
                    $("#loader").show(''); //MOSTRAMOS EL LOADER.
                },
                url: "clientesbloqueados_controlador.php?op=buscar_clientesbloqueados",
                type: "post",
                data: {vendedor: vendedor},
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
    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var vendedor = sessionStorage.getItem("vendedor");
    if (vendedor !== "") {
        window.location = "clientesbloqueados_excel.php?vendedor="+vendedor;
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var vendedor = sessionStorage.getItem("vendedor");
    if (vendedor !== "") {
        window.open('clientesbloqueados_pdf.php?&vendedor='+vendedor, '_blank');
    }
});


function mostrar() {
    var vendedor = $("#vendedor").val();
    $.post("clientesbloqueados_controlador.php?op=mostrar", {vendedor: vendedor}, function (data, status) {
        data = JSON.parse(data);

        $("#cuenta").html(data.cuenta);

    });
}

init();
