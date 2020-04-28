var tabla_inventarioglobal;
var estado_minimizado;
var depo

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    $("#loader").hide();
    estado_minimizado = false;
}

function limpiar() {
  document.getElementById('depo[]').value = "";
}

function validarCantidadRegistrosTabla() {
    (tabla_inventarioglobal.rows().count() === 0)
        ? estado = true : estado = false;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

$(document).ready(function () {
    $("#depo").change(() => estado_minimizado = true)
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_inventarioglobal", function () {
    depo = document.getElementById('depo[]').value;
   
        if (depo !== "") {
            var datos = $('#frminventario').serialize();
            /*  sessionStorage.setItem("vendedor", vendedor); */
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla_inventarioglobal = $('#inventarioglobal_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    beforeSend: function () {
                        $("#loader").show(''); //MOSTRAMOS EL LOADER.
                    },
                    url: "inventarioglobal_controlador.php?op=buscar_inventarioglobal",
                    type: "post",
                    data: datos,
                    error: function (e) {
                        console.log(e.responseText);
                    },
                    complete: function () {

                        $("#tabla").show('');//MOSTRAMOS LA TABLA.
                        $("#loader").hide();//OCULTAMOS EL LOADER.
                        $("#minimizar").slideToggle();
                        /* validarCantidadRegistrosTabla(); */
                        /*  mostrar(); */
                        /* limpiar(); */
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
           
        } else {
        Swal.fire('Atención!', 'Debe seleccionar al menos un Almacén!', 'error');
        return (false);
     } 
     
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click", "#btn_excel", function () {
    var vendedor = sessionStorage.getItem("vendedor");
    if (vendedor !== "") {
        window.location = "clientesbloqueados_excel.php?vendedor=" + vendedor;
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click", "#btn_pdf", function () {
    var vendedor = sessionStorage.getItem("vendedor");
    if (vendedor !== "") {
        window.open('clientesbloqueados_pdf.php?&vendedor=' + vendedor, '_blank');
    }
});

/* function mostrar() {
    var texto= 'Clientes Bloqueados: ';
    var cuenta =(tabla_inventarioglobal.rows().count());
    $("#cuenta").html(texto + cuenta);
} */

init();
