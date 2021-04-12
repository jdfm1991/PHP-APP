
let colspanActivacion;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    colspanActivacion = $('#cabecera_activacion').attr('colSpan');

    listar_marcas();
    listar_kpi();
}

$(document).ready(function(){
    $('table').columntoggle({
        //Class of column toggle contains toggle link
        toggleContainerClass:'columntoggle-container',
        //Text in column toggle box
        toggleLabel:'MOSTRAR/OCULTAR CELDAS: ',
        //the prefix of key in localstorage
        keyPrefix:'columntoggle-',
        //keyname in localstorage, if empty, it will get from URL
        key:''

    });
});

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true : estado = false;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}


function listar_kpi(){

    let fechai    = $('#fechai').val();
    let fechaf    = $('#fechaf').val();
    let d_habiles = $('#d_habiles').val();
    let d_trans   = $('#d_trans').val();

    $.ajax({
        url: "kpi_controlador.php?op=listar_kpi",
        method: "POST",
        data: {fechai: fechai, fechaf: fechaf, d_habiles: d_habiles, d_trans:d_trans},
        beforeSend: function () {
            // limpiar_grafico();
            $("#loader").show(''); /*MOSTRAMOS EL LOADER.*/
            $("#spinner").css('visibility', 'visible');
        },
        error: function (e) {
            console.log(e.responseText);
        },
        success: function (data) {
            data = JSON.parse(data);


        },
        complete: function () {
            $("#spinner").css('visibility', 'hidden');
            $("#loader").hide();//OCULTAMOS EL LOADER.
        }
    });
}

function listar_marcas() {
    $.ajax({
        async: false,
        url: "kpi_controlador.php?op=listar_marcaskpi",
        type: "GET",
        error: function (e) {
            console.log(e.responseText);
        },
        success: function (data) {
            data = JSON.parse(data);

            if(!jQuery.isEmptyObject(data.lista_marcaskpi)){
                $('#cabecera_activacion').attr('colSpan', (parseInt(colspanActivacion) + parseInt(data.lista_marcaskpi.length)) );

                $.each(data.lista_marcaskpi, function(idx, opt) {
                    $('table thead #cells').find('th:nth-child('+3+')').after(
                        '<th style="width: 20px;"><div class="small align-middle" style="width: 10px; word-wrap: break-word; text-align: center">' + opt + '</div></th>'
                    );
                });
            }
        }
    });
}

function limpiar_grafico() {
    if (barChart) {
        barChart.clear();
        barChart.destroy();
    }
}

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    // var depos = sessionStorage.getItem("depos");
    // var marcas = sessionStorage.getItem("marcas");
    // var orden = sessionStorage.getItem("orden");
    // var p1 = sessionStorage.getItem("p1");
    // var p2 = sessionStorage.getItem("p2");
    // var p3 = sessionStorage.getItem("p3");
    // var iva = sessionStorage.getItem("iva");
    // var cubi = sessionStorage.getItem("cubi");
    // var exis = sessionStorage.getItem("exis");
    // window.location = "listadeprecio_excel.php?&depos="+depos+"&marcas="+marcas+""+"&orden="+orden+"&p1="+p1+"&p2="+p2+"&p3="+p3+"&iva="+iva+"&cubi="+cubi+"&exis="+exis;
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    // var depos = sessionStorage.getItem("depos");
    // var marcas = sessionStorage.getItem("marcas");
    // var orden = sessionStorage.getItem("orden");
    // var p1 = sessionStorage.getItem("p1");
    // var p2 = sessionStorage.getItem("p2");
    // var p3 = sessionStorage.getItem("p3");
    // var iva = sessionStorage.getItem("iva");
    // var cubi = sessionStorage.getItem("cubi");
    // var exis = sessionStorage.getItem("exis");
    // window.open("listadeprecio_pdf.php?&depos="+depos+"&marcas="+marcas+""+"&orden="+orden+"&p1="+p1+"&p2="+p2+"&p3="+p3+"&iva="+iva+"&cubi="+cubi+"&exis="+exis, '_blank');
});

init();