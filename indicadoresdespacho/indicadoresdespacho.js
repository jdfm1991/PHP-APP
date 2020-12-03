var estado_minimizado;

// 1-efectivas  2-rechazo  3-oportunidad
var indicador_seleccionado;

let array_selects = [
    'pills-efectivas',
    'pills-rechazo',
    'pills-oportunidad'
];

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    array_selects.forEach( pill => {
        $('#'+pill+' #fechai').val("2016-01-01");
        $('#'+pill+' #fechaf').val("2020-11-21");
    });
    // $("#fechaf").val("");
    $("#tabla").hide();
    $('#grafico').hide();
    $("#loader").hide();
    estado_minimizado = false;
    indicador_seleccionado = 2;
    listar_choferes();
    // listar_causas_de_rechazo(); PENDIENTE TERMINAR
    switch (indicador_seleccionado) {
        case 1: $("#pills-fectivas-tab").trigger("click");    break;
        case 2: $("#pills-rechazo-tab").trigger("click");     break;
        case 3: $("#pills-oportunidad-tab").trigger("click"); break;
    }
}

function limpiar() {
    array_selects.forEach( pill => {
        $('#'+pill+' #fechai').val("");
        $('#'+pill+' #fechaf').val("");
        $('#'+pill+' #chofer').val("");
        $('#'+pill+' #causa').val("");
    });
}

function validarCantidadRegistrosTabla(data) {
    (data.length === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function () {
    ($("#fechai").val() !== "" && $("#fechaf").val() !== "" && $("#chofer").val() !== "" &&
        (indicador_seleccionado!==2 || (indicador_seleccionado===2 && $("#pills-rechazo #causa").val() !== "")))
        ? estado_minimizado = true : estado_minimizado = false;
};

$(document).ready(function(){
    $("#fechai").change(() => no_puede_estar_vacio());
    $("#fechaf").change(() => no_puede_estar_vacio());
    $("#chofer").change(() => no_puede_estar_vacio());
    $("#causa").change(() => no_puede_estar_vacio());

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

        array_selects.forEach( pill => {
            $chofer = $('#'+pill+' #chofer');

            //lista de seleccion de choferes
            $chofer.append('<option name="" value="">Seleccione</option>');
            if(indicador_seleccionado===2) {
                $chofer.append('<option name="" value="-">Todos</option>');
            }
            $chofer.append('<option name="" value="5589533" selected>prueba</option>');
            $.each(data.lista_choferes, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $chofer.append('<option name="" value="' + opt.Cedula +'">' + opt.Nomper + '</option>');
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
    let listar;
    let title;

    switch (indicador_seleccionado) {
        case 1:
            listar = "listar_entregas_efectivas";
            title = $("#pills-fectivas-tab").text().trim();
            formData = $("#efectivas_form").serializeArray();
            break;
        case 2:
            listar = "listar_causas_rechazo";
            title = $("#pills-rechazo-tab").text().trim();
            formData = $("#rechazo_form").serializeArray();
            break;
        case 3:
            listar = "";
            title = $("#pills-oportunidad-tab").text().trim();
            formData = $("#oportunidad_form").serializeArray();
            break;
    }

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#grafico").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if ((formData[0]['name'] === "fechai" && formData[0]['value'] !== "")
            && (formData[1]['name'] === "fechaf" && formData[1]['value'] !== "")
            && (formData[2]['name'] === "chofer" && formData[2]['value'] !== "")
            && (indicador_seleccionado!==2 || (indicador_seleccionado===2 && formData[3]['name'] === "causa" && formData[3]['value'] !== ""))
        ) {
            sesionStorageItems(
                formData[0]['value'],
                formData[1]['value'],
                formData[2]['value'],
                (indicador_seleccionado===2) ? formData[3]['value'] : ""
            );

            //reordenamos el array serializado para volverlo array asociativo.
            let form_data = {};
            formData.forEach( val => { form_data[val.name] = val.value } );


            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            $.ajax({
                url: "indicadoresdespacho_controlador.php?op="+listar,
                method: "POST",
                data: form_data,
                beforeSend: function () {
                    $("#loader").show(''); /*MOSTRAMOS EL LOADER.*/
                },
                error: function (e) {
                    console.log(e.responseText);
                },
                success: function (data) {
                    data = JSON.parse(data);
                    $(".title-card").text(title);
                    $("#tabla").show('');//MOSTRAMOS LA TABLA.
                    $("#grafico").show('');//MOSTRAMOS EL GRAFICO.

                    //limpiamos la tabla
                    $('#tabla tbody').empty();

                    //llenamos inputs date disabled
                    $("#fechai_disabled").val(formData[0]['value']);
                    $("#fechaf_disabled").val(formData[1]['value']);

                    //proceso de llenado del grafico
                    construirGrafico(data);

                    //proceso de llenado de la tabla
                    construirTabla(data.tabla);

                    //llenado de los span
                    llenadoDeSpan(data);

                    $("#loader").hide();//OCULTAMOS EL LOADER.
                    validarCantidadRegistrosTabla(data.tabla);
                    limpiar();//LIMPIAMOS EL SELECTOR.

                }
            });

            estado_minimizado = true;
        }
    } else {
        if (formData[0]['name'] === "fechai" && formData[0]['value'] === "") {
            Swal.fire('Atención!','Seleccione una fecha inicial!','error');
            return (false);
        }
        if (formData[1]['name'] === "fechaf" && formData[1]['value'] === "") {
            Swal.fire('Atención!','Seleccione una fecha final!','error');
            return (false);
        }
        if (formData[2]['name'] === "chofer" && formData[2]['value'] === "") {
            Swal.fire('Atención!','Seleccione un chofer!','error');
            return (false);
        }
        if (indicador_seleccionado===2 && formData[3]['name'] === "causa" && formData[3]['value'] === "") {
            Swal.fire('Atención!','Seleccione una causa de rechazo!','error');
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

function construirGrafico(data) {
    let object;

    switch (indicador_seleccionado) {
        case 1:
            object = entregas_efectivas(data);
            break;
        case 2:
            break;
        case 3:
            break;
    }

    //CONSTRUCCION DEL GRAFICO
    var barChart = new Chart($('#barChart').get(0).getContext('2d'), {
        type: 'bar',
        data: jQuery.extend(true, {}, {
            labels  : object.labels,
            datasets: []
        }),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            datasetFill: false,
            tooltips: {
                mode: 'label'
            },
            scales: {
                yAxes: [{
                    ticks: {
                        suggestedMin: 0,
                        suggestedMax: (object.value_max > 25) ? object.value_max : 25
                    }
                }],
            },
        }
    });


    object.content.forEach( val => {

        barChart.data.datasets.push({
            label               : val.label,
            type                : val.type,
            backgroundColor     : val.color,
            borderColor         : val.color,
            pointRadius         : val.pointRadius,
            pointStrokeColor    : val.color,
            pointHighlightFill  : '#fff',
            pointHighlightStroke: val.color,
            fill                : val.fill,
            data                : val.values
        });

    });

}

function construirTabla(data){

    if(!jQuery.isEmptyObject(data)) {
        $.each(data, function(idx, opt) {
            $('#indicadores_data')
                .append(
                    '<tr>' +
                    '<td align="center" class="small align-middle">' + opt.fecha_entrega + '</td>' +
                    '<td align="center" class="small align-middle">' + opt.ped_despachados + '</td>' +
                    '<td align="center" class="small align-middle">' + parseInt(opt.porc_efectividad * 10) / 10 + ' %</td>' +
                    '<td align="center" class="small align-middle">' + opt.ordenes_despacho + '</td>' +
                    '</tr>'
                );
        });
    } else {
        //en caso de consulta vacia, mostramos un mensaje de vacio
        $('#indicadores_data').append('<tr><td colspan="4" align="center">Sin registros para esta Consulta</td></tr>');
    }
}

function llenadoDeSpan(data){
    $fact = $("#fact_label");
    let totalDespacho;
    let pedporliquidar;
    let pedentregados;
    let promediodiario;

    if(!jQuery.isEmptyObject(data)) {
        totalDespacho  = data.totaldespacho;
        pedporliquidar = data.total_ped_porliquidar;
        pedentregados  = data.total_ped_entregados;
        promediodiario = data.promedio_diario_despacho;
    } else {
        totalDespacho  = 0;
        pedporliquidar = 0;
        pedentregados  = 0;
        promediodiario = 0;
    }

    $("#datos_chofer").val(data.chofer);
    $("#total_ped_camion").text(totalDespacho);
    $("#total_ped_pendiente").text(pedporliquidar);
    $("#total_ped_entregados").text(pedentregados);
    $("#promedio_diario_despachos").text(promediodiario);

    $("#ordenes_despacho").text(data.ordenes_despacho.substr(0, data.ordenes_despacho.length-2));
    $("#fact_sinliquidar").text(data.fact_sinliquidar.substr(1, data.fact_sinliquidar.length));

    switch(indicador_seleccionado){
        case 1:
            $fact.text('FACTURAS SIN LIQUIDAR');
            break;
        case 2:
            $fact.text('CAUSA DEL RECHAZO');
            break;
        default:
            $fact.text('');
    }
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
