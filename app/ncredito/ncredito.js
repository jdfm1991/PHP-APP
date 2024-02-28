var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = true;
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true : estado = false;
    $('#btn_excel').attr("disabled", estado);
}

var no_puede_estar_vacio = function () {
    ($("#fechai").val() !== "" && $("#fechaf").val() !== "" )
        ? estado_minimizado = true : estado_minimizado = false;
};


$(document).ready(function () {
    $("#fechai").change(() => no_puede_estar_vacio());
    $("#fechaf").change(() => no_puede_estar_vacio());
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {
    $("#ncredito_data").dataTable().fnDestroy();
    var fechai = $("#fechai").val();
    var fechaf = $("#fechaf").val();
    var tipo = $("#tipo").val();


    $("#tabla").hide();
    $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
    estado_minimizado = false;
    sessionStorage.setItem("fechai", fechai);
    sessionStorage.setItem("fechaf", fechaf);
    sessionStorage.setItem("tipo", tipo);
    let isError = false;
    if (fechai !== "" && fechaf !== "") {
        //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
        tabla = $('#ncredito_data').DataTable({
            "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
            "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
            "ajax": {
                url: "ncredito_controlador.php?op=buscar_ncredito",
                type: "post",
                dataType: "json",
                data: { fechai: fechai, fechaf: fechaf, tipo: tipo },
                beforeSend: function () {
                    SweetAlertLoadingShow();
                },
                error: function (e) {
                    isError = SweetAlertError(e.responseText, "Error!")
                    send_notification_error(e.responseText);
                    console.log(e.responseText);
                }, success: function (data) {

                    $("#tabla").show('');//MOSTRAMOS LA TABLA.

                    //creamos una matriz JSON para aoColumnDefs
                    var aryJSONColTable = [];

                    $.each(data.columns, function (i, opt) {
                        aryJSONColTable.push({ "sTitle": opt, "aTargets": [i] });
                    });

                    tabla = $('#ncredito_data').DataTable({
                        "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                        "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                        "sEcho": data.sEcho, //INFORMACION PARA EL DATATABLE
                        "iTotalRecords": data.iTotalRecords, //TOTAL DE REGISTROS AL DATATABLE.
                        "iTotalDisplayRecords": data.iTotalDisplayRecords, //TOTAL DE REGISTROS A VISUALIZAR.
                        "data": data.aaData, // informacion por registro
                        //CodigoDinamico
                        "aoColumnDefs": aryJSONColTable,
                        "bProcessing": true,
                        "bLengthChange": true,
                        "bFilter": true,
                        "bScrollCollapse": true,
                        "bJQueryUI": true,
                        //finCodigoDinamico
                        "bDestroy": true,
                        "bInfo": true,
                        "iDisplayLength": 10,
                        "order": [[1, "desc"]],
                        "language": texto_español_datatables
                    });

                    if (tipo == 'B') {

                        $('#total_registros').html(

                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Saldo Total:   <code>' + data.saldo + '  Bs</code>   '
                        )

                    } else {


                        $('#total_registros').html(

                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Saldo Total:   <code>' + data.saldo + '  $</code>   '
                        )

                    }



                    validarCantidadRegistrosTabla();
                    limpiar();
                },
                complete: function () {
                    if (!isError) SweetAlertLoadingClose();
                }
            },

        });





    }

});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click", "#btn_excel", function () {
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    var tipo = sessionStorage.getItem("tipo", tipo);

    window.location = "ncredito_excel.php?&fechai=" + fechai + "&fechaf=" + fechaf + "&tipo=" + tipo;

});


init();