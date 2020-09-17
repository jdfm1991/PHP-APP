var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#loader").hide();
    $("#tabla").hide();
    estado_minimizado = false;
    listar_vendedores();
    listar_canales();
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
    $("#tipo").val("");
    $("#vendedores").val("");
    $("#checkbox").prop("checked", false);
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true : estado = false;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function () {
    ($("#fechai").val() !== "" && $("#fechaf").val() !== "" && $("#tipo").val() !== "" && $("#vendedores").val() !== "")
        ? estado_minimizado = true : estado_minimizado = false;
};

$(document).ready(function () {
    $("#fechai").change(() => no_puede_estar_vacio());
    $("#fechaf").change(() => no_puede_estar_vacio());
    $("#tipo").change(() => no_puede_estar_vacio());
    $("#vendedores").change(() => no_puede_estar_vacio());
});


//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_factsindes", function () {

    var fechai = $('#fechai').val();
    var fechaf = $('#fechaf').val();
    var tipo = $('#tipo').val();
    var vendedores = $('#vendedores').val();
    var check = ($('#checkbox:checked').val()!=null);


    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (fechai !== "" && fechaf !== "" && tipo !== "" && vendedores !== "") {
            sesionStorageItems(fechai, fechaf, tipo, vendedores, check);
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            $.ajax({
                async: true,
                url: "facturassindespachar_controlador.php?op=listar",
                method: "POST",
                data: {'fechai': fechai,'fechaf': fechaf,'tipo': tipo, 'vendedores': vendedores, 'check': check,},
                beforeSend: function () {
                    $("#loader").show(''); //MOSTRAMOS EL LOADER.
                    if(tabla instanceof $.fn.dataTable.Api){
                        $('#tablafactsindes').DataTable().clear().destroy();
                    }
                    $('#tablafactsindes thead').empty();
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

                    tabla = $('#tablafactsindes').DataTable({
                        "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                        "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                        "sEcho": data.sEcho, //INFORMACION PARA EL DATATABLE
                        "iTotalRecords": data.iTotalRecords, //TOTAL DE REGISTROS AL DATATABLE.
                        "iTotalDisplayRecords": data.iTotalDisplayRecords, //TOTAL DE REGISTROS A VISUALIZAR.
                        "data": data.aaData, // informacion por registro
                        //CodigoDinamico
                        "aoColumnDefs": aryJSONColTable,
                        // "bProcessing": true,
                        "bLengthChange": true,
                        "bFilter": true,
                        "bScrollCollapse": true,
                        "bJQueryUI": true,
                        //finCodigoDinamico
                        "bDestroy": true,
                        "responsive": (check) ? true : false,
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

        if (fechai === ""){
            Swal.fire('Atención!', 'Debe Colocar la fecha inicial!', 'error');
            return false;
        }
        if (fechaf === "" ){
            Swal.fire('Atención!', 'Debe Colocar la fecha final!', 'error');
            return false;
        }
        if (vendedores === ""){
            Swal.fire('Atención!', 'Debe Seleccionar una Ruta!', 'error');
            return false;
        }
        if (tipo === ""){
            Swal.fire('Atención!', 'Debe Seleccionar un Tipo!', 'error');
            return false;
        }
    }
});

function sesionStorageItems(fechai, fechaf, tipo, vendedores, check){
    sessionStorage.setItem("fechai", fechai);
    sessionStorage.setItem("fechaf", fechaf);
    sessionStorage.setItem("tipo", tipo);
    sessionStorage.setItem("vendedores", vendedores);
    sessionStorage.setItem("check", check);
}

function listar_vendedores(){
    $.post("../clientesbloqueados/clientesbloqueados_controlador.php?op=listar_vendedores", function(data){
        data = JSON.parse(data);

        if(!jQuery.isEmptyObject(data.lista_vendedores)){
            //lista de seleccion de vendedores
            $('#vendedores')
                .append('<option name="" value="">Seleccione un Vendedor o Ruta</option>')
                .append('<option name="" value="-">Todos</option>');
            $.each(data.lista_vendedores, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#vendedores').append('<option name="" value="' + opt.CodVend +'">' + opt.CodVend + ': ' + opt.Descrip.substr(0, 35) + '</option>');
            });
        }
    });
}

function listar_canales(){
    $.post("facturassindespachar_controlador.php?op=listar_canales", function(data){
        data = JSON.parse(data);

        if(!jQuery.isEmptyObject(data.lista_canales)){
            //lista de seleccion de vendedores
            $('#tipo')
                .append('<option name="" value="">Seleccione Tipo</option>')
                .append('<option name="" value="-">Todos</option>');
            $.each(data.lista_canales, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#tipo').append('<option name="" value="' + opt.Clase +'">' + opt.Clase + '</option>');
            });
        }
    });
}

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var fechai = sessionStorage.getItem("fechai");
    var fechaf = sessionStorage.getItem("fechaf");
    var tipo = sessionStorage.getItem("tipo");
    var vendedores = sessionStorage.getItem("vendedores");
    var check = sessionStorage.getItem("check");
    window.location = "listadeprecio_excel.php?&depos="+depos+"&marcas="+marcas+""+"&orden="+orden+"&p1="+p1+"&p2="+p2+"&p3="+p3+"&iva="+iva+"&cubi="+cubi+"&exis="+exis;
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai");
    var fechaf = sessionStorage.getItem("fechaf");
    var tipo = sessionStorage.getItem("tipo");
    var vendedores = sessionStorage.getItem("vendedores");
    var check = sessionStorage.getItem("check");
    window.open("listadeprecio_pdf.php?&depos="+depos+"&marcas="+marcas+""+"&orden="+orden+"&p1="+p1+"&p2="+p2+"&p3="+p3+"&iva="+iva+"&cubi="+cubi+"&exis="+exis, '_blank');
});

init();
