var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#loader").hide();
    $("#tabla").hide();
    estado_minimizado = false;
    // listar_vendedores();
}

function limpiar() {
    /*$("#opc").val("");
    $("#vendedor").val("");*/
}

$(document).ready(function () {
    $("#depo").change(() => no_puede_estar_vacio());
    $("#marca").change(() => no_puede_estar_vacio());
    $("#orden").change(() => no_puede_estar_vacio());
});

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true : estado = false;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function () {
    ($("#depo").val() !== "" && $("#marca").val() !== "" && $("#orden").val() !== "") ? estado_minimizado = true : estado_minimizado = false;
};

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_listadeprecio", function () {

    var depos = $('#depo').val();
    var marcas = $('#marca').val();
    var orden = $('#orden').val();
    var p1 = 0;
    var p2 = 0;
    var p3 = 0;
    var iva = 1;
    var cubi = 0;
    var exis = 0;

    if (depos === "") {
        Swal.fire('Atención!','Seleccione un Almacen!','error');
        return (false);
    }
    if (marcas === "") {
        Swal.fire('Atención!','Seleccione al menos una Marca!','error');
        return (false);
    }
    if (orden === "") {
        Swal.fire('Atención!',' Seleccione como desea Ordenar!','error');
        return (false);
    }
    if (document.getElementById('p1').checked) { p1 = 1; }
    if (document.getElementById('p2').checked) { p2 = 1; }
    if (document.getElementById('p3').checked) { p3 = 1; }
    if (document.getElementById('iva').checked) { iva = 1.16; }
    if (document.getElementById('cubi').checked) { cubi = 1; }
    if (document.getElementById('exis').checked) { exis = 1; }

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (depos !== "" && marcas !== "" && orden !== "") {
            sessionStorage.setItem("depos", depos);
            sessionStorage.setItem("marcas", marcas);
            sessionStorage.setItem("orden", orden);
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla = $('#tablaprecios').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    beforeSend: function () {
                        $("#loader").show(''); //MOSTRAMOS EL LOADER.
                    },
                    url: "listadeprecio_controlador.php?op=listar",
                    type: "post",
                    data: {'depo': depos,'marca': marcas,'orden': orden,'p1': p1, 'p2': p2, 'p3': p3, 'iva': iva, 'cubi': cubi, 'exis':exis,},
                    error: function (e) {
                        console.log(e.responseText);
                        Swal.fire('Atención!','ha ocurrido un error!','error');
                    },
                    complete: function () {
                        $("#tabla").show('');//MOSTRAMOS LA TABLA.
                        $("#loader").hide();//OCULTAMOS EL LOADER.
                        validarCantidadRegistrosTabla();
                        //limpiar();//LIMPIAMOS EL SELECTOR.
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

/*//ACCION AL PRECIONAR EL BOTON EXCEL.
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
*/
/*function mostrar() {

    var texto= 'Clientes Sin Transacción:  ';
    var cuenta =(tabla_costodeinventario.rows().count());
    $("#cuenta").html(texto + cuenta);
}*/

init();
