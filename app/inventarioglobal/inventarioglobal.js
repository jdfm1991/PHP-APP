var tabla_inventarioglobal;
var estado_minimizado;
var depo;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    $("#loader").hide();
    estado_minimizado = false;

    listar_almacenes();

    $("#checkbox").on("click", function (e) {
        if($("#checkbox").is(':checked') ) {
            $(".depo > option").prop("selected","selected");
        } else {
            $(".depo > option").prop("selected","");
        }
        $(".depo").trigger("change");
    });
}

function limpiar() {
    $('#checkbox').prop('checked', false);
    $('[name="depo[]"]').val("").trigger("change");
    $('[name="depo[]"]').attr("disabled", false);
    $('#btn_excel').attr("disabled", false);
    $('#btn_pdf').attr("disabled", false);
    $('#tfoot_cantbul_x_des').html("");
    $('#tfoot_cantpaq_x_des').html("");
    $('#tfoot_cantbul_sistema').html("");
    $('#tfoot_cantpaq_sistema').html("");
    $('#tfoot_totalbul_inv').html("");
    $('#tfoot_totalpaq_inv').html("");
    $('#cuenta').text("");
}

function validarCantidadRegistrosTabla() {
    (tabla_inventarioglobal.rows().count() === 0)
        ? estado = true : estado = false;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function () {
    ($(".depo").val().length > 0) ? estado_minimizado = true : estado_minimizado = false;
};

$(document).ready(function () {
    $(".depo").change(() => no_puede_estar_vacio());
});

function listar_almacenes() {
    $.ajax({
        url: "../costodeinventario/costodeinventario_controlador.php?op=listar_depositos",
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


//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_inventarioglobal", function () {

    var depo = $('[name="depo[]"]').val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (depo.length > 0) {
            var datos = $('#frminventario').serialize();
            //almacenamos en sesion una variable
            sessionStorage.setItem("datos", datos);
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            $.ajax({
                beforeSend: function () {
                    $("#loader").show();
                },
                type: "POST",
                url: "inventarioglobal_controlador.php?op=listar_inventarioglobal",
                data: datos,
                error: function(X){
                    Swal.fire('Atención!','ha ocurrido un error!','error');
                },
                success: function(data) {
                    data = JSON.parse(data);
                    $("#tabla").show();
                    $("#loader").hide();

                    //TABLA
                    tabla_inventarioglobal = $('#inventarioglobal_data').dataTable({
                        "aProcessing": true,//Activamos el procesamiento del datatables
                        "aServerSide": true,//Paginación y filtrado realizados por el servidor

                        "sEcho": data.contenido_tabla.sEcho, //INFORMACION PARA EL DATATABLE
                        "iTotalRecords": data.contenido_tabla.iTotalRecords, //TOTAL DE REGISTROS AL DATATABLE.
                        "iTotalDisplayRecords": data.contenido_tabla.iTotalDisplayRecords, //TOTAL DE REGISTROS A VISUALIZAR.
                        "aaData": data.contenido_tabla.aaData, // informacion por registro

                        "bDestroy": true,
                        "responsive": true,
                        "bInfo": true,
                        "iDisplayLength": 8,//Por cada 8 registros hace una paginación
                        'columnDefs' : [{
                            'visible': false, 'targets': [0]
                        }],
                        "language": texto_español_datatables
                    }).DataTable();

                    $('#tfoot_cantbul_x_des').html(data.totales_tabla.tbulto);
                    $('#tfoot_cantpaq_x_des').html(data.totales_tabla.tpaq);
                    $('#tfoot_cantbul_sistema').html(data.totales_tabla.tbultsaint);
                    $('#tfoot_cantpaq_sistema').html(data.totales_tabla.tpaqsaint);
                    $('#tfoot_totalbul_inv').html(data.totales_tabla.tbultoinv);
                    $('#tfoot_totalpaq_inv').html(data.totales_tabla.tpaqinv);
                    $('#cuenta').text("Total Facturas sin Despachar: " + data.totales_tabla.facturas_sin_despachar);

                    validarCantidadRegistrosTabla();
                    limpiar();//LIMPIAMOS EL SELECTOR.
                    estado_minimizado = true;
                }
            });

        }
    } else {
        Swal.fire('Atención!', 'Debe seleccionar al menos un Almacén!', 'error');
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var datos = sessionStorage.getItem("datos");
    if (datos !== "") {
        window.location = 'inventarioglobal_excel.php?&'+datos;
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var datos = sessionStorage.getItem("datos");
    if (datos !== "") {
        window.open('inventarioglobal_pdf.php?&'+datos, '_blank');
    }
});

init();