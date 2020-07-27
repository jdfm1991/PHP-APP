var tabla_sellin;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    $("#loader").hide();
    estado_minimizado = false;
    listar_marcas();
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
    $("#marca").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla_sellin.rows().count() === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

function listar_marcas() {
    $.post("sellin_controlador.php?op=listar_marcas", function(data){
        data = JSON.parse(data);

        if(!jQuery.isEmptyObject(data.lista_marcas)){
            //lista de seleccion de vendedores
            $('#marca')
                .append('<option name="" value="">Seleccione una opción</option>')
                .append('<option name="" value="-">TODAS</option>');
            $.each(data.lista_marcas, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#marca').append('<option name="" value="' + opt.marca +'">' + opt.marca + '</option>');
            });
        }
    });
}

var no_puede_estar_vacio = function()
{
    ($("#fechai").val() !== "" && $("#fechaf").val() !== "" && $("#marca").val() !== "")
        ? estado_minimizado = true : estado_minimizado = false ;
};

$(document).ready(function(){
    $("#fechai").change( () => no_puede_estar_vacio() );
    $("#fechaf").change( () => no_puede_estar_vacio() );
    $("#marca").change( () => no_puede_estar_vacio() );
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_sellin", function () {

    var fechai = $("#fechai").val();
    var fechaf = $("#fechaf").val();
    var marca = $("#marca").val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (fechai !== "" && fechaf !== "" && marca !== "") {
            sessionStorage.setItem("fechai", fechai);
            sessionStorage.setItem("fechaf", fechaf);
            sessionStorage.setItem("marca", marca);
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla_sellin = $('#sellin_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    beforeSend: function () {
                        $("#loader").show(''); //MOSTRAMOS EL LOADER.
                    },
                    url: "sellin_controlador.php?op=buscar_sellin",
                    type: "post",
                    data: {fechai: fechai, fechaf: fechaf, marca: marca},
                    error: function (e) {
                        console.log(e.responseText);
                    },
                    complete: function () {

                        $("#tabla").show('');//MOSTRAMOS LA TABLA.
                        $("#loader").hide();//OCULTAMOS EL LOADER.
                        validarCantidadRegistrosTabla();
                       /* mostrar()*/
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
        Swal.fire('Atención!', 'Debe seleccionar un rango de fecha y un marca.', 'error');
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
   var fechai = sessionStorage.getItem("fechai", fechai);
   var fechaf = sessionStorage.getItem("fechaf", fechaf);
   var marca = sessionStorage.getItem("marca", marca);
   if (fechai !== "" && fechaf !== "" && marca !== "") {
    window.location = "sellin_excel.php?&fechai="+fechai+"&fechaf="+fechaf+"&marca="+marca;
}
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    var marca = sessionStorage.getItem("marca", marca);
    if (fechai !== "" && fechaf !== "" && marca !== "") {
        window.open('sellin_pdf.php?&fechai='+fechai+'&fechaf='+fechaf+'&marca='+marca, '_blank');
    }
});

function mostrar() {
   /* var fechai = $("#fechai").val();
    var fechaf = $("#fechaf").val();
    var marca = $("#marca").val();
    $.post("clientes_controlador.php?op=mostrar", {fechai: fechai, fechaf: fechaf, marca: marca}, function (data, status) {
        data = JSON.parse(data);

        $("#cuenta").html(data.cuenta);

    });*/
    var texto= 'Clientes Sin Transacción:  ';
    var cuenta =(tabla_sellin.rows().count());
    $("#cuenta").html(texto + cuenta);
}

init();
