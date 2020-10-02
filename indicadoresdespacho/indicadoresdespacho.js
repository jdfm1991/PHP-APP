var tabla;

var estado_minimizado;

// 1-efectivas  2-rechazo  3-oportunidad
var indicador_seleccionado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    $("#loader").hide();
    estado_minimizado = false;
    indicador_seleccionado = 1;
    listar_choferes();
    // listar_causas_de_rechazo(); PENDIENTE TERMINAR
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
    $("#chofer").val("");
    $("#causa").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

$(document).ready(function(){
    ($("#fechai").val().length > 0 && $("#fechaf").val().length > 0 && $("#chofer").val().length > 0 /*&& $("#causa").val().length > 0*/)
        ? estado_minimizado = true : estado_minimizado = false;

    $("#pills-fectivas-tab").on("click", function (e) {
        indicador_seleccionado = 1;
    });
    $("#pills-rechazo-tab").on("click", function (e) {
        indicador_seleccionado = 2;
    });
    $("#pills-oportunidad-tab").on("click", function (e) {
        indicador_seleccionado = 3;
    });
});

function listar_choferes(){
    $.post("indicadoresdespacho_controlador.php?op=listar_choferes", function(data, status){
        data = JSON.parse(data);

        let array_selects = ['pills-efectivas', 'pills-rechazo', 'pills-oportunidad'];

        array_selects.forEach( pill => {

            //lista de seleccion de choferes
            $('#'+pill+' #chofer').append('<option name="" value="">Seleccione</option>');
            $.each(data.lista_choferes, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#'+pill+' #chofer').append('<option name="" value="' + opt.Cedula +'">' + opt.Nomper + '</option>');
            });
        });
    });
}

function listar_causas_de_rechazo(){
    $.post("indicadoresdespacho_controlador.php?op=listar_choferes", function(data, status){
        data = JSON.parse(data);

        //lista de seleccion de choferes
        $('#causa').append('<option name="" value="">Seleccione Causa del rechazo</option>');
        $.each(data.lista_choferes, function(idx, opt) {
            //se itera con each para llenar el select en la vista
            $('#causa').append('<option name="" value="' + opt.Cedula +'">' + opt.Nomper + '</option>');
        });
    });
}

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {

    let formData;
    switch (indicador_seleccionado) {
        case 1:
            formData = new FormData($("#efectivas_form")[0]);
            break;
        case 2:
            formData = new FormData($("#rechazo_form")[0]);
            break;
        case 3:
            formData = new FormData($("#oportunidad_form")[0]);
            break;
    }

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if ($("#fechai").val() !== "" && $("#fechaf").val() !== "" && $("#chofer").val() !== "" /*&& $("#causa").val() !== ""*/) {
            sesionStorageItems($("#fechai").val() !== "", $("#fechaf").val() !== "", $("#chofer").val() !== "" /*, $("#causa").val() !== ""*/);
            sessionStorage.setItem("fechai", fechai);
            sessionStorage.setItem("fechaf", fechaf);
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla = $('#historicocostos_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    beforeSend: function () {
                        $("#loader").show(''); //MOSTRAMOS EL LOADER.
                    },
                    url: "historicocostos_controlador.php?op=listar",
                    type: "post",
                    data: {fechai: fechai, fechaf: fechaf},
                    error: function (e) {
                        console.log(e.responseText);
                    },
                    complete: function () {

                        $("#tabla").show('');//MOSTRAMOS LA TABLA.
                        $("#loader").hide();//OCULTAMOS EL LOADER.
                        validarCantidadRegistrosTabla();
                        // mostrar();
                        limpiar();//LIMPIAMOS EL SELECTOR.

                    }
                },//TRADUCCION DEL DATATABLE.
                "bDestroy": true,
                "responsive": true,
                "bInfo": true,
                "iDisplayLength": 10,
                // "order": [[0, "desc"]],
                "language": texto_español_datatables
            });
            estado_minimizado = true;
        }
    } else {
        if (fechai === "") {
            Swal.fire('Atención!','Seleccione una fecha inicial!','error');
            return (false);
        }
        if (fechaf === "") {
            Swal.fire('Atención!','Seleccione una fecha final!','error');
            return (false);
        }
    }
});

function sesionStorageItems(fechai, fechaf, chofer, causa = ""){
    sessionStorage.setItem("fechai", fechai);
    sessionStorage.setItem("fechaf", fechaf);
    sessionStorage.setItem("chofer", chofer);
    sessionStorage.setItem("causa", causa);
}

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var fechai = sessionStorage.getItem("fechai");
    var fechaf = sessionStorage.getItem("fechaf");
    if (fechai !== "" && fechaf !== "") {
        window.location = "historicocostos_excel.php?fechai="+fechai+"&fechaf="+fechaf;
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai");
    var fechaf = sessionStorage.getItem("fechaf");
    if (fechai !== "" && fechaf !== "") {
        window.open('historicocostos_pdf.php?&fechai='+fechai+"&fechaf="+fechaf, '_blank');
    }
});

init();
