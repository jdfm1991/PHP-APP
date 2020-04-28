var tabla_por_despachar;

var tabla_despachos;

var estado_minimizado;

var registros_por_despachar;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla_facturas_por_despachar").hide();
    $("#tabla_detalle_despacho").hide();
    $("#loader1").hide();
    $('.verFactura').attr("disabled", true);
    $('.porDespachar').attr("disabled", true);
    $('.nextBtn').attr("disabled", true);
    $('.generar').attr("disabled", true);
    registros_por_despachar = [];
    estado_minimizado = false;
}

function limpiar() {
    $("#fecha").val("");
    $("#chofer").val("");
    $("#vehiculo").val("");
    $("#destino").val("");
    $("#factura").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla_despachos.rows().count() === 0)
        ? estado = true : estado = false;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function () {
    //VALIDA PARA AÑADIR EN LA TABLA FACTURAS POR DESPACHAR DICHO REGISTRO
    estado = !($("#fecha").val().length > 0 && $("#chofer").val().length > 0 && $("#vehiculo").val().length > 0 && $("#destino").val().length > 0 && $("#factura").val().length > 0);
    $('.anadir').attr("disabled", estado); //boton añadir
    estado_minimizado = estado;

    //VALIDA PARA HABILITAR EL BOTON SIGUIENTE
    estado1 = !($("#fecha").val().length > 0 && $("#chofer").val().length > 0 && $("#vehiculo").val().length > 0 && $("#destino").val().length > 0);
    $('.nextBtn').attr("disabled", estado1); //boton siguiente
};

$(document).ready(function () {
    var navListItems = $('div.setup-panel div a'), //botones steps
        allWells = $('.setup-content'), //step-2
        allNextBtn = $('.nextBtn'); //boton siguiente

    allWells.hide();

    navListItems.click(function (e) {
        e.preventDefault();
        var $target = $($(this).attr('href')),
            $item = $(this);

        if (!$item.hasClass('disabled')) {
            navListItems.removeClass('btn-primary').addClass('btn-default');
            $item.addClass('btn-primary');
            allWells.hide();
            $target.show();
            $target.find('input:eq(0)').focus();
        }
    });

    allNextBtn.click(function(){
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            curInputs = curStep.find("input[type='text'],input[type='url']"),
            isValid = true;

        $(".form-group").removeClass("has-error");
        for(var i=0; i<curInputs.length; i++){
            if (!curInputs[i].validity.valid){
                isValid = false;
                $(curInputs[i]).closest(".form-group").addClass("has-error");
            }
        }

        if (isValid)
            nextStepWizard.removeAttr('disabled').trigger('click');
    });

    $('div.setup-panel div a.btn-primary').trigger('click');

    //MUESTRA LA TABLA SI TIENE AL MENOS UN DESPACHO CARGADO EN MEMORIA POR REGISTRAR
    //Y HABILITA EL BOTON GENERAR DESPACHO
    if(registros_por_despachar.length > 0){
        $('.generar').attr("disabled", false);//boton generar
        $("#tabla_facturas_por_despachar").show();
    } else {
        $('.generar').attr("disabled", true);//boton generar
        $("#tabla_facturas_por_despachar").hide();
    }

    $("#fecha").change(() => no_puede_estar_vacio());
    $("#chofer").change(() => no_puede_estar_vacio());
    $("#vehiculo").change(() => no_puede_estar_vacio());
    $("#destino").on('keyup', () => no_puede_estar_vacio()).keyup();
    $("#factura").on('keyup', () => no_puede_estar_vacio()).keyup();

});

function validarPeso(){
    //
}

function validarFacturaEnDespacho(){
    //
}

function validarExistenciaFactura(){
    //
}

//ACCION AL PRECIONAR EL BOTON AÑADIR.
$(document).on("click", ".anadir", function () {

    var fecha = $("#fecha").val();
    var chofer = $("#chofer").val();
    var vehiculo = $("#vehiculo").val();
    var destino = $("#destino").val();
    var factura = $("#factura").val();

    if (estado_minimizado) {
        $("#tabla_facturas_por_despachar").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (fecha !== "" && chofer !== "" && vehiculo !== "" && destino !== "" && factura !== "") {
            sessionStorage.setItem("fecha", fecha);
            sessionStorage.setItem("chofer", chofer);
            sessionStorage.setItem("vehiculo", vehiculo);
            sessionStorage.setItem("destino", destino);
            sessionStorage.setItem("factura", factura);
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla_por_despachar = $('#fact_por_despachar_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    beforeSend: function () {
                        $("#loader1").show(''); //MOSTRAMOS EL LOADER.
                    },
                    url: "clientescodnestle_controlador.php?op=buscar_clientescodnestle",
                    type: "post",
                    data: {fecha: fecha, chofer: chofer, vehiculo: vehiculo, destino: destino, factura: factura},
                    error: function (e) {
                        console.log(e.responseText);
                    },
                    complete: function () {

                        $("#tabla").show('');//MOSTRAMOS LA TABLA.
                        $("#loader").hide();//OCULTAMOS EL LOADER.
                        validarCantidadRegistrosTabla();
                        //mostrar();
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
        Swal.fire('Atención!', 'Debe seleccionar un rango de fecha y un vendedor.', 'error');
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
/*$(document).on("click", "#btn_excel", function () {
    var opc = sessionStorage.getItem("opc", opc);
    var vendedor = sessionStorage.getItem("vendedor");
    if (opc !== "" && vendedor !== "") {
        window.location = "clientescodnestle_excel.php?&opc=" + opc + "&vendedor=" + vendedor;
    }
});*/

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click", "#btn_pdf", function () {
    var opc = sessionStorage.getItem("opc", opc);
    var vendedor = sessionStorage.getItem("vendedor");
    if (opc !== "" && vendedor !== "") {
        window.open('clientescodnestle_pdf.php?&opc=' + opc + '&vendedor=' + vendedor, '_blank');
    }
});





init();
