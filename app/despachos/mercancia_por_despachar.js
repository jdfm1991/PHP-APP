let tabla_mercancia;
let estado_minimizado_tabla_modal;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla_mercancia_por_despachar").slideUp();
    estado_minimizado_tabla_modal = false;

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

function limpiar_campo_almacenes_modal() {
    $('#checkbox').prop('checked', false);
    $('[name="depo[]"]').val("").trigger("change");
    $('[name="depo[]"]').attr("disabled", false);
}

function limpiar_modal_mercancia() {
    limpiar_campo_almacenes_modal();
    $('#relacion_mercancia tbody').empty();
    $('[name="depo[]"]').empty().trigger("change");
    $("#tabla_mercancia_por_despachar").slideUp();
    listar_almacenes();
}

function listar_almacenes() {
    let isError = false;
    $.ajax({
        url: "despachos_controlador.php?op=listar_depositos",
        type: "GET",
        dataType: "json",
        beforeSend: function () {
            //mientras carga inabilitamos el select
            $('[name="depo[]"]').attr("disabled", true);
            SweetAlertLoadingShow();
        },
        error: function(e){
            isError = SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function(data) {
            //cuando termina la consulta, activamos el boton
            $('[name="depo[]"]').attr("disabled", false);
            //lista de seleccion de depositos
            $.each(data.lista_depositos, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('[name="depo[]"]').append('<option name="" value="' + opt.codubi +'">' + opt.codubi + ': '+ opt.descrip.substr(0, 35) + '</option>');
            });
        },
        complete: function () {
            if(!isError) {
                SweetAlertLoadingClose();

                $(".depo").change(() => {
                    ($(".depo").val().length > 0)
                        ? estado_minimizado_tabla_modal = true
                        : estado_minimizado_tabla_modal = false;
                });
            }
        }
    });
}

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btnBuscarmercanciaModal", function () {

    const depo = $('[name="depo[]"]').val();

    if (estado_minimizado_tabla_modal) {
        estado_minimizado_tabla_modal = false;
        if (depo.length > 0) {
            const datos = $('#frminventario').serialize();
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            $.ajax({
                url: "despachos_controlador.php?op=listar_mercancia_por_despachar",
                type: "POST",
                data: datos,
                dataType: "json",
                beforeSend: function () {
                    $("#tabla_mercancia_por_despachar").slideUp();
                    $('#relacion_mercancia tbody').empty();
                    SweetAlertLoadingShow();
                },
                error: function (e) {
                    isError = SweetAlertError(e.responseText, "Error!")
                    console.log(e.responseText);
                },
                success: function(data) {
                    let {contenido_tabla, totales_tabla} = data;
                    //TABLA
                    tabla_mercancia = $('#relacion_mercancia').dataTable({
                        "aProcessing": true,//Activamos el procesamiento del datatables
                        "aServerSide": true,//Paginación y filtrado realizados por el servidor

                        "sEcho": contenido_tabla.sEcho, //INFORMACION PARA EL DATATABLE
                        "iTotalRecords": contenido_tabla.iTotalRecords, //TOTAL DE REGISTROS AL DATATABLE.
                        "iTotalDisplayRecords": contenido_tabla.iTotalDisplayRecords, //TOTAL DE REGISTROS A VISUALIZAR.
                        "aaData": contenido_tabla.aaData, // informacion por registro

                        "bDestroy": true,
                        "responsive": true,
                        "bInfo": true,
                        "iDisplayLength": 8,//Por cada 8 registros hace una paginación
                        'columnDefs' : [{
                            'visible': false, 'targets': [0]
                        }],
                        "language": texto_español_datatables
                    }).DataTable();

                    //$('#cuenta').text("Total Facturas sin Despachar: " + totales_tabla.facturas_sin_despachar);

                    limpiar_campo_almacenes_modal();//LIMPIAMOS EL SELECTOR.
                    estado_minimizado_tabla_modal = true;
                },
                complete: function () {
                    if(!isError) {
                        SweetAlertLoadingClose();
                        $("#tabla_mercancia_por_despachar").slideDown();//MOSTRAMOS LA TABLA.
                    }
                }
            });

        }
    } else {
        SweetAlertError('Debe seleccionar al menos un Almacén!');
        return (false);
    }
});


init();