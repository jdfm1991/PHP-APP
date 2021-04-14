
let colspanRutas;
let colspanActivacion;
let colspanEfectividad;
let colspanVentas;
let colspanTotal;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    colspanRutas       = $('#cabecera_rutas').attr('colSpan');
    colspanActivacion  = $('#cabecera_activacion').attr('colSpan');
    colspanEfectividad = $('#cabecera_efectividad').attr('colSpan');
    colspanVentas      = $('#cabecera_ventas').attr('colSpan');

    colspanTotal = colspanRutas + colspanActivacion + colspanEfectividad + colspanVentas;

    listar_marcas();
    listar_kpi();
}

$(document).ready(function(){

});

function listar_kpi(){

    let fechai    = $('#fechai').val();
    let fechaf    = $('#fechaf').val();
    let d_habiles = $('#d_habiles').val();
    let d_trans   = $('#d_trans').val();

    $.ajax({
        url: "kpi_controlador.php?op=listar_kpi",
        method: "POST",
        data: {fechai: fechai, fechaf: fechaf, d_habiles: d_habiles, d_trans:d_trans},
        dataType: "json", // Formato de datos que se espera en la respuesta
        beforeSend: function () {
            swal.fire({
                html: '<h5>Procesando información, espere...</h5>',
                showConfirmButton: false,
                allowOutsideClick: false,
                onRender: function() {
                    // there will only ever be one sweet alert open.
                    $('.swal2-content').prepend(sweet_loader);
                }
            });
        },
        error: function (e) {
            console.log(e.responseText);
        },
        success: function (datos) {
            if(!jQuery.isEmptyObject(datos.tabla)){
                $.each(datos.tabla, function(idx, opt) {
                    let { coordinador, data, subtotal } = opt

                    $('#tabla').append('<tr><td class="text-left" style="font-weight: bold" colspan="'+colspanTotal+'">' + coordinador.toUpperCase() + '</td></tr>');

                    $.each(data, function(idx, opt) {
                        $('#tabla').append(obtenerInfoTabla(opt));
                    });

                    $('#tabla').append(obtenerInfoTabla(subtotal, true, true));
                });
            }
            $('#tabla').append('<tr><td colspan="'+colspanTotal+'">'+ "" +'</td></tr>');
            $('#tabla').append(obtenerInfoTabla(datos.total_general, true, true));
        },
        complete: function () {
            swal.fire({
                icon: 'success',
                showConfirmButton: false,
                timer: 1000,
                html: '<h5>Carga completa!</h5>'
            });

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

function obtenerInfoTabla(opt, negrita=false, colorFull=false) {
    isBold  = negrita===true ? 'style="font-weight: bold"' : "";

    let valuesMarcas = '';
    opt.marcas.forEach( val => { valuesMarcas += '<td align="center" class="small align-middle" '+isBold+'>' + val.valor + '</td>' });

    return  '<tr>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.ruta + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.maestro + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.activos + '</td>' +
            valuesMarcas +
            td_withprogress(opt.porc_activacion, isBold, colorFull) +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.por_activar + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.visita + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.obj_documentos_mensual + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.facturas_realizadas + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.notas_realizadas + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.devoluciones_realizadas + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.montoendivisa_devoluciones + '</td>' +
            td_withprogress(opt.efec_alcanzada_fecha, isBold, colorFull) +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.objetivo_bulto + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.logro_bulto + '</td>' +
            td_withprogress(opt.porc_alcanzado_bulto, isBold, colorFull) +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.objetivo_kg + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.logro_kg + '</td>' +
            td_withprogress(opt.porc_alcanzado_kg, isBold, colorFull) +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.drop_size_divisas + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.objetivo_ventas_divisas + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.logro_ventas_divisas + '</td>' +
            td_withprogress(opt.porc_alcanzado_ventas_divisas, isBold, colorFull) +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.logro_ventas_divisas_pepsico + '</td>' +
            td_withprogress(opt.porc_alcanzado_ventas_divisas_pepsico, isBold, colorFull) +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.logro_ventas_divisas_complementaria + '</td>' +
            td_withprogress(opt.porc_alcanzado_ventas_divisas_complementaria, isBold, colorFull) +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.cobranzas_rebajadas + '</td>' +
            '</tr>';
}

let td_withprogress = (valor, isBold, withCellColor) => {
    if(withCellColor)
        return '<td align="center" class="small align-middle '+semaforo(valor)+'" '+isBold+'>' + valor + ' %</td>'
    return '<td align="center" class="small align-middle" '+isBold+'>' + obtenerProgress(valor) + '</td>'
}

function obtenerProgress(valor) {
    valor = parseFloat(valor.replace('.','').replace(',','.'));
    return '<span>'+valor+'%</span>' +
        '<div class="progress progress-xs">' +
                '<div class="progress-bar '+semaforo(valor)+'" style="width: '+valor+'%"></div>' +
            '</div>'
}

function semaforo(valor) {
    let bg;
    if (!is_float(valor)) {
        valor = parseFloat(valor.toString().replace('.','').replace(',','.'));
    }
    if (valor > 80){
        bg = "bg-success";
    }else if (valor > 50 && valor <= 80){
        bg = "bg-warning";
    }else if (valor <= 50){
        bg = "bg-danger";
    }
    return bg;
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