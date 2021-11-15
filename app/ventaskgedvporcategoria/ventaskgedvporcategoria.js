var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;
    listar_vendedores();
    listar_marcas();
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
    $("#vendedor").val("");
    $("#marca").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function()
{
    ($("#fechai").val() !== "" && $("#fechaf").val() !== ""
        && $("#vendedor").val() !== "" && $("#marca").val() !== "")
        ? estado_minimizado = true : estado_minimizado = false ;
};

function listar_vendedores() {
    $.ajax({
        url: "ventaskgedvporcategoria_controlador.php?op=listar_vendedores",
        method: "POST",
        dataType: "json",
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data.lista_vendedores)){
                //lista de seleccion
                $('#vendedor').append('<option name="" value="">--Seleccione vendedor--</option>');
                $('#vendedor').append('<option name="" value="-">TODOS</option>');
                $.each(data.lista_vendedores, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#vendedor').append('<option name="" value="' + opt.CodVend +'">' + opt.CodVend + ': ' + opt.Descrip.substr(0, 35) + '</option>');
                });
            }
        }
    });
}

function listar_marcas() {
    $.ajax({
        url: "ventaskgedvporcategoria_controlador.php?op=listar_marcas",
        method: "POST",
        dataType: "json",
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data.lista_marcas)){
                //lista de seleccion
                $('#marca').append('<option name="" value="">--Seleccione marca--</option>');
                $('#marca').append('<option name="" value="-">TODAS</option>');
                $.each(data.lista_marcas, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#marca').append('<option name="" value="' + opt.marca +'">' + opt.marca + '</option>');
                });
            }
        }
    });
}

$(document).ready(function(){
    $("#fechai").change( () => no_puede_estar_vacio() );
    $("#fechaf").change( () => no_puede_estar_vacio() );
    $("#vendedor").change( () => no_puede_estar_vacio() );
    $("#marca").change( () => no_puede_estar_vacio() );
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {

    var fechai = $("#fechai").val();
    var fechaf = $("#fechaf").val();
    var vendedor = $("#vendedor").val();
    var marca = $("#marca").val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (fechai !== "" && fechaf !== "" && vendedor !== "" && marca !== "") {
            sessionStorage.setItem("fechai", fechai);
            sessionStorage.setItem("fechaf", fechaf);
            sessionStorage.setItem("vendedor", vendedor);
            sessionStorage.setItem("marca", marca);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla = $('#ventaskgedvporcategoria_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    url: "ventaskgedvporcategoria_controlador.php?op=listar",
                    type: "post",
                    dataType: "json",
                    data: {fechai: fechai, fechaf: fechaf, vendedor: vendedor, marca: marca},
                    beforeSend: function () {
                        SweetAlertLoadingShow();
                    },
                    error: function (e) {
                        isError = SweetAlertError(e.responseText, "Error!")
                        console.log(e.responseText);
                    },
                    complete: function () {
                        if(!isError) SweetAlertLoadingClose();
                        $("#tabla").show('');//MOSTRAMOS LA TABLA.
                        validarCantidadRegistrosTabla();
                        mostrar()
                        limpiar();//LIMPIAMOS EL SELECTOR.
                    }
                },//TRADUCCION DEL DATATABLE.
                "bDestroy": true,
                "responsive": true,
                "bInfo": true,
                "iDisplayLength": 10,
                "order": [[0, "asc"]],
                'columnDefs':[
                    {
                        'visible': true,
                        'targets': [0]
                    }
                ],
                "language": texto_español_datatables
            });
            estado_minimizado = true;
        }
    } else {
        SweetAlertError('Debe seleccionar un rango de fecha, marca y un vendedor.');
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    var vendedor = sessionStorage.getItem("vendedor", vendedor);
    var marca = sessionStorage.getItem("marca", marca);
    if (fechai !== "" && fechaf !== "" && vendedor !== "" && marca !== "") {
        window.location = "ventaskgedvporcategoria_excel.php?&fechai="+fechai+"&fechaf="+fechaf+"&vendedor="+vendedor+"&vendedor="+vendedor;
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    var vendedor = sessionStorage.getItem("vendedor", vendedor);
    var marca = sessionStorage.getItem("marca", marca);
    if (fechai !== "" && fechaf !== "" && vendedor !== "" && marca !== "") {
        window.open('ventaskgedvporcategoria_pdf.php?&fechai='+fechai+'&fechaf='+fechaf+'&marca='+marca+"&marca="+marca, '_blank');
    }
});

init();
