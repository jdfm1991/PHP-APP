var tabla_listadeprecio;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    $("#loader").hide();
    estado_minimizado = false;
}

function limpiar() {
    $("#depo").val("");
    $("#marca").val("");
    $("#orden").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla_listadeprecio.rows().count() === 0)
    ? estado = true : estado = false;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function () {
    ($("#depo").val() !== "" && $("#marca").val() !== "" && $("#orden").val() !== "") ? estado_minimizado = true : estado_minimizado = false;
};

$(document).ready(function () {
    $("#depo").change(() => no_puede_estar_vacio());
    $("#marca").change(() => no_puede_estar_vacio());
    $("#orden").change(() => no_puede_estar_vacio());
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_listadeprecio", function () {

    var depos = document.getElementById('depo').value;
    var marcas = document.getElementById('marca').value;
    var orden = document.getElementById('orden').value;
    var p1 = 0;
    var p2 = 0;
    var p3 = 0;
    var iva = 1;
    var cubi = 0;
    var exis = 0;

    if (depos == "") {
        Swal.fire('Atención!','Seleccione un Almacen!','error');
        return (false);
    }
    if (marcas == "") {
        Swal.fire('Atención!','Seleccione una Marca!','error');
        return (false);
    }
    if (orden == "") {
        Swal.fire('Atención!','¿Como desea ordenar el resultado?','error');
        return (false);
    }
    if (document.getElementById('p1').checked) { p1 = 1; }
    if (document.getElementById('p2').checked) { p2 = 1; }
    if (document.getElementById('p3').checked) { p3 = 1; }
    if (document.getElementById('iva').checked) { iva = 1.16; }
    if (document.getElementById('cubi').checked) { cubi = 1; }
    if (document.getElementById('exis').checked) { exis = 1; }

    /*    if (estado_minimizado) {*/
       /* $("#tabla").hide();
        $("#minimizar").slideToggle();*////MINIMIZAMOS LA TARJETA.
        /*estado_minimizado = false;*/
        if (depos != "" && marcas != "" && orden != "") {
            /*sessionStorage.setItem("opc", opc);
            sessionStorage.setItem("vendedor", vendedor);*/
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla_listadeprecio = $('#listaeprecio_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    beforeSend: function () {
                        $("#loader").show(''); //MOSTRAMOS EL LOADER.
                    },
                    url: "listadeprecio.php",
                    type: "post",
                    data: {'depo': depos,'marca': marcas,'orden': orden,'p1': p1, 'p2': p2, 'p3': p3, 'iva': iva, 'cubi': cubi, 'exis':exis,} ,
                    error: function (e) {
                        console.log(e.responseText);
                    },
                    complete: function () {

                       /* $("#tabla").show('');//MOSTRAMOS LA TABLA.*/
                        $("#loader").hide();//OCULTAMOS EL LOADER.
                     /*   validarCantidadRegistrosTabla();*/
                        /* mostrar()*/
                        /*limpiar();*///LIMPIAMOS EL SELECTOR.

                    }
                },//TRADUCCION DEL DATATABLE.
              /*  "bDestroy": true,
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
                },*/
            });
            estado_minimizado = true;
        }
   /* } else {

   }*/
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
/*$(document).on("click", "#btn_excel", function () {
    var opc = sessionStorage.getItem("opc", opc);
    var vendedor = sessionStorage.getItem("vendedor");
    if (opc !== "" && vendedor !== "") {
        window.location = "clientescodnestle_excel.php?&opc=" + opc + "&vendedor=" + vendedor;
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click", "#btn_pdf", function () {
    var opc = sessionStorage.getItem("opc", opc);
    var vendedor = sessionStorage.getItem("vendedor");
    if (opc !== "" && vendedor !== "") {
        window.open('clientescodnestle_pdf.php?&opc=' + opc + '&vendedor=' + vendedor, '_blank');
    }
});*/

init();
