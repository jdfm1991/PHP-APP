var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#loader").hide();
    $("#tabla").hide();
    estado_minimizado = false;
    listar_depositos_marcas();
}

function limpiar() {
    $("#depo").val("");
    $("#marca").val("");
    $("#orden").val("");
    $("#p1").prop("checked", false);
    $("#p2").prop("checked", false);
    $("#p3").prop("checked", false);
    $("#iva").prop("checked", true);
    $("#cubi").prop("checked", false);
    $("#exis").prop("checked", true);
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true : estado = false;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function () {
    ($("#depo").val() !== "" && $("#marca").val() !== "" && $("#orden").val() !== "") ? estado_minimizado = true : estado_minimizado = false;
};

$(document).ready(function () {
    $("#depo").change(() => no_puede_estar_vacio());
    $("#marca").change(() => no_puede_estar_vacio());
    $("#orden").change(() => no_puede_estar_vacio());
});


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
            $.ajax({
                async: true,
                url: "listadeprecio_controlador.php?op=listar",
                method: "POST",
                data: {'depo': depos,'marca': marcas,'orden': orden,'p1': p1, 'p2': p2, 'p3': p3, 'iva': iva, 'cubi': cubi, 'exis':exis,},
                beforeSend: function () {
                    $("#loader").show(''); //MOSTRAMOS EL LOADER.
                    if(tabla instanceof $.fn.dataTable.Api){
                        $('#tablaprecios').DataTable().clear().destroy();
                    }
                    $('#tablaprecios thead').empty();
                },
                error: function (e) {
                    console.log(e.responseText);
                    Swal.fire('Atención!','ha ocurrido un error!','error');
                },
                success: function (data) {
                    data = JSON.parse(data);

                    $("#tabla").show('');//MOSTRAMOS LA TABLA.
                    $("#loader").hide();//OCULTAMOS EL LOADER.

                    //creamos una matriz JSON para aoColumnDefs
                    var aryJSONColTable = [];
                    /*var aryColTableChecked = data.columns;
                    for (var i = 0; i <aryColTableChecked.length; i ++) {
                        aryJSONColTable.push ({
                            "sTitle": aryColTableChecked[i],
                            "aTargets": [i]
                        });
                    }*/
                    $.each(data.columns, function(i, opt) {
                        aryJSONColTable.push({"sTitle": opt, "aTargets": [i]});
                    });

                    tabla = $('#tablaprecios').DataTable({
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
                        "responsive": true,
                        "bInfo": true,
                        "iDisplayLength": 10,
                        "language": texto_español_datatables
                    });

                    validarCantidadRegistrosTabla();
                    limpiar();
                }
            });


            estado_minimizado = true;
        }
    } else {

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
    }
});

function listar_depositos_marcas(){

    $.post("listadeprecio_controlador.php?op=listar_depositos_marcas", function(data){
        data = JSON.parse(data);

        $('#depo').append('<option name="" value="">Seleccione Almacen</option>');
        $.each(data.lista_depositos, function(idx, opt) {
            //se itera con each para llenar el select en la vista
            $('#depo').append('<option name="" value="' + opt.codubi +'">'+ opt.codubi +': '+ opt.descrip.substr(0, 35) + '</option>');
        });

        $('#marca').append('<option name="" value="">Seleccione una Marca</option>').append('<option name="" value="-">TODAS</option>');
        $.each(data.lista_marcas, function(idx, opt) {
            $('#marca').append('<option name="" value="' + opt.marca +'">' + opt.marca + '</option>');
        });
    });


}

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
