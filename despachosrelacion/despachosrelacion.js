var tabla_relacion_despachos;

var tabla_despachos;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    listarRelacionDespachos();

}

function limpiar() {
    /*$("#fecha").val("");
    $("#chofer").val("");
    $("#vehiculo").val("");
    $("#destino").val("");
    $("#factura").val("");
    registros_por_despachar = "";
    peso_max_vehiculo = 0;
    peso_acum_facturas = 0;
    valor_bg_progreso = "bg-success";*/
}

function limpiar_campo_factura() {
    $("#nrodocumento").val("");
    $("#detalle_despacho").html("");
}

function agregarCeros(fact){
    var cad_cero="";
    for(var i=0;i<(6-fact.length);i++)
        cad_cero+=0;
    return cad_cero+fact;
}

function modalEditarDespachos(correlativo) {
    $.post("despachosrelacion_controlador.php?op=buscar_cabeceraDespacho",{correlativo : correlativo}, function(data, status)
    {
        data = JSON.parse(data);

        console.log(data);

        $('#editarDespachoModal').modal('show');

        $("#detalle_despacho").html(data.mensaje);

    });
}

function listarRelacionDespachos() {
    tabla_relacion_despachos = $('#relacion_data').dataTable({

        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax":
            {
                url: 'despachosrelacion_controlador.php?op=listar_RelacionDespachos',
                type: "get",
                dataType: "json",
                error: function (e) {
                    console.log(e.responseText);
                }
            },

        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,//Por cada 10 registros hace una paginación
        "order": [[0, "desc"]],//Ordenar (columna,orden)
        'columnDefs':[{
                "targets": 3, // your case first column
                "className": "text-center",
                // "width": "4%"
        }],
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

function buscarFacturaEnDespachos(nrofact){
    if (nrofact !== "") {
        nrofact = agregarCeros(nrofact);
        $("#detalle_despacho").html("");
        $.post("despachosrelacion_controlador.php?op=buscar_facturaEnDespachos", {nrfactb: nrofact}, function(data, status){
            data = JSON.parse(data);
            $("#detalle_despacho").html(data.mensaje);
        });
    } else {
        $("#detalle_despacho").html("");
    }
}

//ACCION AL PRECIONAR EL BOTON BUSCAR.
$(document).on("click", "#btnBuscarFactModal", function () {
    var fact = $("#nrodocumento").val();
    buscarFacturaEnDespachos(fact);
});

init();