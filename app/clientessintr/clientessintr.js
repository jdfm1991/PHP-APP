var tabla_clientessintr;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    $("#loader").hide();
    estado_minimizado = false;
    listar_vendedores();
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
    $("#vendedor").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla_clientessintr.rows().count() === 0)
        ? estado = true : estado = false;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

function f() {
    var date = new Date();

    $("#fechaf").datepicker({
        todayBtn: "linked",
        minDate: date,
        maxDate: +7
    });
}

var no_puede_estar_vacio = function () {
    ($("#fechai").val() !== "" && $("#fechaf").val() !== "" && $("#vendedor").val() !== "")
        ? estado_minimizado = true : estado_minimizado = false;
};

function listar_vendedores() {
    $.post("../clientesbloqueados/clientesbloqueados_controlador.php?op=listar_vendedores", function(data){
        data = JSON.parse(data);

        if(!jQuery.isEmptyObject(data.lista_vendedores)){
            //lista de seleccion de vendedores
            $('#vendedor').append('<option name="" value="">Seleccione un Vendedor o Ruta</option>');
            $.each(data.lista_vendedores, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#vendedor').append('<option name="" value="' + opt.CodVend +'">' + opt.CodVend + ': ' + opt.Descrip.substr(0, 35) + '</option>');
            });
        }
    });
}

$(document).ready(function () {
    $("#fechai").change(() => no_puede_estar_vacio());
    $("#fechaf").change(() => no_puede_estar_vacio());
    $("#vendedor").change(() => no_puede_estar_vacio());

    $("#fechaf").datepicker({
        todayBtn: "linked",
        minDate: date,
        maxDate: +7
    });
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_clientessintr", function () {

    var fechai = $("#fechai").val();
    var fechaf = $("#fechaf").val();
    var vendedor = $("#vendedor").val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (fechai !== "" && fechaf !== "" && vendedor !== "") {
            sessionStorage.setItem("fechai", fechai);
            sessionStorage.setItem("fechaf", fechaf);
            sessionStorage.setItem("vendedor", vendedor);
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla_clientessintr = $('#clientessintr_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    beforeSend: function () {
                        $("#loader").show(''); //MOSTRAMOS EL LOADER.
                    },
                    url: "clientessintr_controlador.php?op=buscar_clientessintr",
                    type: "post",
                    data: { fechai: fechai, fechaf: fechaf, vendedor: vendedor },
                    error: function (e) {
                        console.log(e.responseText);
                    },
                    complete: function () {

                        $("#tabla").show('');//MOSTRAMOS LA TABLA.
                        $("#loader").hide();//OCULTAMOS EL LOADER.
                        validarCantidadRegistrosTabla();
                        mostrar();
                        limpiar();//LIMPIAMOS EL SELECTOR.

                    }
                },//TRADUCCION DEL DATATABLE.
                "bDestroy": true,
                "responsive": true,
                "bInfo": true,
                "iDisplayLength": 10,
                "order": [[0, "desc"]],
                "language": texto_español_datatables
            });
            estado_minimizado = true;
        }
    } else {
        Swal.fire('Atención!', 'Debe seleccionar un rango de fecha y un vendedor.', 'error');
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click", "#btn_excel", function () {
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    var vendedor = sessionStorage.getItem("vendedor", vendedor);
    if (fechai !== "" && fechaf !== "" && vendedor !== "") {
        window.location = "clientessintr_excel.php?&fechai=" + fechai + "&fechaf=" + fechaf + "&vendedor=" + vendedor;
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click", "#btn_pdf", function () {
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    var vendedor = sessionStorage.getItem("vendedor", vendedor);
    if (fechai !== "" && fechaf !== "" && vendedor !== "") {
        window.open('clientessintr_pdf.php?&fechai=' + fechai + '&fechaf=' + fechaf + '&vendedor=' + vendedor, '_blank');
    }
});

function mostrar() {
    var texto = 'Clientes Sin Transacción:  ';
    var cuenta = (tabla_clientessintr.rows().count());
    $("#cuenta").html(texto + cuenta);
}

init();
