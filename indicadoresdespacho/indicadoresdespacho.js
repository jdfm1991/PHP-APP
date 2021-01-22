let estado_minimizado;

// 1-efectivas  2-rechazo  3-oportunidad
let indicador_seleccionado;

let array_selects = [
    'pills-efectivas',
    'pills-rechazo',
    'pills-oportunidad'
];

let barChart;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla1").hide();
    $("#tabla2").hide();
    $('#grafico').hide();
    $("#loader").hide();
    estado_minimizado = false;
    indicador_seleccionado = 1;
    listar_choferes();
    limpiar();
    switch (indicador_seleccionado) {
        case 1: $("#pills-fectivas-tab").trigger("click");    break;
        case 2: $("#pills-rechazo-tab").trigger("click");     break;
        case 3: $("#pills-oportunidad-tab").trigger("click"); break;
    }
}

function limpiar() {
    array_selects.forEach( pill => {
        $('#'+pill+' #chofer').val("");
        $('#'+pill+' #causa').val("");
        $('#'+pill+' #tipoPeriodo').val("");
        $('#'+pill+' #periodo').val("");
        $('#'+pill+' #tipoPeriodo').prop("disabled", true);
        $('#'+pill+' #periodo').prop("disabled", true);
    });
    $('[name="ped_pendiente"]').show();
    $('[name="diario_despachos"]').show();
}

function limpiar_grafico() {
    if (barChart) {
        barChart.clear();
        barChart.destroy();
    }
}

function validarCantidadRegistrosTabla(data) {
    (data.length === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

let no_puede_estar_vacio = function () {
    let pill = array_selects[indicador_seleccionado - 1];

    ($("#" + pill + " #tipoPeriodo").val() !== "" && $("#" + pill + " #periodo").val() !== "" && $("#" + pill + " #chofer").val() !== "" &&
        (indicador_seleccionado !== 2 || (indicador_seleccionado === 2 && $("#" + pill + " #causa").val() !== "")))
        ? estado_minimizado = true : estado_minimizado = false;
};

let es_habilidado = function () {
    let pill = array_selects[indicador_seleccionado - 1];

    if($("#" + pill + " #chofer").val() !== "") {
        $("#" + pill + " #tipoPeriodo").prop("disabled", false);
    } else {
        $('#' + pill + ' #tipoPeriodo').val("");
        $('#' + pill + ' #periodo').val("");
        $("#" + pill + " #tipoPeriodo").prop("disabled", true);
        $("#" + pill + " #periodo").prop("disabled", true);
    }

    if($("#" + pill + " #tipoPeriodo").val() !== "") {
        listar_periodos();
    } else {
        $('#' + pill + ' #periodo').val("");
        $("#" + pill + " #periodo").prop("disabled", true);
    }
}

$(document).ready(function(){
    array_selects.forEach( pill => {
        $("#"+pill+" #periodo").change(() => no_puede_estar_vacio());
        $("#"+pill+" #chofer").change(() => { 
            no_puede_estar_vacio(); 
            es_habilidado();
        });
        $("#"+pill+" #tipoPeriodo").change(() => {
            no_puede_estar_vacio();
            es_habilidado();
        });
        $("#"+pill+" #causa").change(() => no_puede_estar_vacio());
    });

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
    $.post("../choferes/chofer_controlador.php?op=listar_choferes", function(data, status){
        data = JSON.parse(data);

        array_selects.forEach( pill => {
            $chofer = $('#'+pill+' #chofer');

            //lista de seleccion de choferes
            $chofer.append('<option name="" value="">Seleccione chofer</option>');
            if(pill === 'pills-rechazo')
                $chofer.append('<option name="" value="-">Todos</option>');
            $.each(data.lista_choferes, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $chofer.append('<option name="" value="' + opt.Cedula +'">' + opt.Nomper + '</option>');
            });
        });
    });
}

function listar_periodos(){
    let pill = array_selects[indicador_seleccionado - 1];

    let tipoPeriodo = $("#"+pill+" #tipoPeriodo").val();
    let chofer_id = $("#"+pill+" #chofer").val();

    $.post("indicadoresdespacho_controlador.php?op=listar_periodos&s="+indicador_seleccionado, 
    {tipoPeriodo: tipoPeriodo, chofer_id: chofer_id}, 
    function(data, status) {
        data = JSON.parse(data);

        $('#'+pill+' #periodo').empty();

        if(data.error) {
            $('#'+pill+' #periodo').append('<option name="" value="">' + data.error + '</option>');
        } 
        else {
            $('#'+pill+' #periodo').prop("disabled", false);
        //lista de seleccion de periodos
        $('#'+pill+' #periodo').append('<option name="" value="">Seleccione periodo</option>');
        $.each(data, function(idx, opt) {
            //se itera con each para llenar el select en la vista
            $('#'+pill+' #periodo').append('<option name="" value="' + opt.value +'">' + opt.label + '</option>');
        });
        }
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
            listar = "listar_oportunidad_despacho";
            title = $("#pills-oportunidad-tab").text().trim();
            formData = $("#oportunidad_form").serializeArray();
            break;
    }

    if (estado_minimizado) {
        $("#tabla1").hide();
        $("#tabla2").hide();
        $("#grafico").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if ((formData[0]['name'] === "chofer" && formData[0]['value'] !== "")
            && (formData[1]['name'] === "tipoPeriodo" && formData[1]['value'] !== "")
            && (formData[2]['name'] === "periodo" && formData[2]['value'] !== "")
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
                    limpiar_grafico();
                    $("#loader").show(''); /*MOSTRAMOS EL LOADER.*/
                },
                error: function (e) {
                    console.log(e.responseText);
                },
                success: function (data) {
                    data = JSON.parse(data);
                    $(".title-card").text(title);
                    limpiar();//LIMPIAMOS EL SELECTOR.

                    
                    $("#tabla1").show('');//MOSTRAMOS LA TABLA.
                    $("#grafico").show('');//MOSTRAMOS EL GRAFICO.

                    //limpiamos la tabla
                    $('#indicadores_data tbody').empty();
                    condicion_tipoperiodo = formData[1]['value'] === "Anual";

                    //llenamos inputs date disabled
                    $("#fechai_disabled").val(data.fechai);
                    $("#fechaf_disabled").val(data.fechaf);

                    //proceso de llenado del grafico
                    construirGrafico(data, condicion_tipoperiodo);

                    //proceso de llenado de la tabla
                    construirTabla(data.tabla, condicion_tipoperiodo);

                    //llenado de los span
                    llenadoDeSpan(data);
                    

                    $("#loader").hide();//OCULTAMOS EL LOADER.
                    validarCantidadRegistrosTabla(data.tabla);
                }
            });

            estado_minimizado = true;
        }
    } else {
        if (formData[0]['name'] === "chofer" && formData[0]['value'] === "") {
            Swal.fire('Atención!','Seleccione un chofer!','error');
            return (false);
        }
        if (formData[1]['name'] === "tipoPeriodo" && formData[1]['value'] === "") {
            Swal.fire('Atención!','Seleccione un Tipo de Periodo!','error');
            return (false);
        }
        if (formData[2]['name'] === "periodo" && formData[2]['value'] === "") {
            Swal.fire('Atención!','Seleccione un Periodo!','error');
            return (false);
        }
        if (indicador_seleccionado===2 && formData[3]['name'] === "causa" && formData[3]['value'] === "") {
            Swal.fire('Atención!','Seleccione una causa de rechazo!','error');
            return (false);
        }
    }
});

function construirGrafico(data, condicion_visibilidad_mes) {
    let object, value_max_default;

    switch (indicador_seleccionado) {
        case 1:
            object = entregas_efectivas(data, condicion_visibilidad_mes);
            value_max_default = 25;
            break;
        case 2:
            object = rechazo_de_los_clientes(data, condicion_visibilidad_mes);
            value_max_default = 8;
            break;
        case 3:
            object = rechazo_de_los_clientes(data);
            value_max_default = 8;
            break;
    }

    //CONSTRUCCION DEL GRAFICO
    barChart = new Chart($('#barChart').get(0).getContext('2d'), {
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
                        suggestedMax: (object.value_max >= 25) ? object.value_max+5 : value_max_default
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

function construirTabla(data, incluye_ordenes){

    $('#indicadores_data thead').empty();

    switch(indicador_seleccionado){
        case 1: $('#indicadores_data thead').append( thead_table_efectivas(incluye_ordenes) ); break;
        case 2: $('#indicadores_data thead').append( thead_table_rechazo(incluye_ordenes) ); break;
        case 3: $('#indicadores_data thead').append( thead_table_oportunidad() ); break;
    }

    if(!jQuery.isEmptyObject(data)) {

        if(indicador_seleccionado===1 || indicador_seleccionado===2) {
            $.each(data, function(idx, opt) {
                ordenes = (!incluye_ordenes) ? '<td align="center" class="small align-middle">' + opt.ordenes_despacho + '</td>' : '';

                $('#indicadores_data')
                    .append(
                        '<tr>' +
                        '<td align="center" class="small align-middle">' + opt.fecha_entrega + '</td>' +
                        '<td align="center" class="small align-middle">' + opt.cant_documentos + '</td>' +
                        '<td align="center" class="small align-middle">' + parseInt(opt.porc * 10) / 10 + ' %</td>' +
                        ordenes +
                        '</tr>'
                    );
            });
        } else if (indicador_seleccionado===3) {
            /* $.each(data, function(idx, opt) {
                $('#indicadores_data')
                    .append(
                        '<tr>' +
                        '<td align="center" class="small align-middle">' + opt.fecha_entrega + '</td>' +
                        '<td align="center" class="small align-middle">' + opt.cant_documentos + '</td>' +
                        '<td align="center" class="small align-middle">' + parseInt(opt.porc * 10) / 10 + ' %</td>' +
                        '<td align="center" class="small align-middle">' + opt.ordenes_despacho + '</td>' +
                        '</tr>'
                    );
            }); */
        }
    } else {
        //en caso de consulta vacia, mostramos un mensaje de vacio
        $('#indicadores_data').append('<tr><td colspan="4" align="center">Sin registros para esta Consulta</td></tr>');
    }
}

function llenadoDeSpan(data){
    $fact = $("#fact_label");
    let totalDespacho;
    let pedporliquidar;
    let total_ped;
    let promediodiario;

    if(indicador_seleccionado !== 3)
    {
        if(!jQuery.isEmptyObject(data)) {
            totalDespacho   = data.totaldespacho;
            total_ped       = data.total_ped;
            if(indicador_seleccionado!==2){
                pedporliquidar = data.total_ped_porliquidar;
                promediodiario = data.promedio_diario_despacho;
            }
        } else {
            totalDespacho   = 0;
            total_ped       = 0;
            pedporliquidar  = 0;
            promediodiario  = 0;
        }

        $("#datos_chofer").val(data.chofer);
        $("#total_ped_camion").text(totalDespacho);
        $("#total_ped_entregados").text(total_ped);

        if(indicador_seleccionado!==2){
            $("#label_tipo").text('entregados');
            $("#total_ped_pendiente").text(pedporliquidar);
            $("#promedio_diario_despachos").text(promediodiario);
        } else {
            $("#label_tipo").text('devueltos');
            $('[name="ped_pendiente"]').hide();
            $('[name="diario_despachos"]').hide();
        }

        if(data.ordenes_despacho.length > 0){
            $("#ordenes_despacho").text(data.ordenes_despacho.substr(0, data.ordenes_despacho.length-2));
        } else {
            $("#ordenes_despacho").text("Sin registros para esta Consulta");
        }
    } else {
        $("#datos_chofer1").val(data.chofer);
        $("#total_promedio").text(data.oportunidad_promedio);
    }

    switch(indicador_seleccionado){
        case 1:
            $fact.text('FACTURAS SIN LIQUIDAR');
            $("#fact_sinliquidar").text(data.fact_sinliquidar.substr(1, data.fact_sinliquidar.length));
            break;
        case 2:
            $fact.text('CAUSA DEL RECHAZO');
            $("#fact_sinliquidar").text(sessionStorage.getItem("causa"));
            break;
    }
}

function sesionStorageItems(chofer, tipoPeriodo, periodo, causa = ""){
    sessionStorage.setItem("chofer", chofer);
    sessionStorage.setItem("tipoPeriodo", tipoPeriodo);
    sessionStorage.setItem("periodo", periodo);
    sessionStorage.setItem("causa", causa);
}

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    const chofer = sessionStorage.getItem("chofer");
    const tipoPeriodo = sessionStorage.getItem("tipoPeriodo");
    const periodo = sessionStorage.getItem("periodo");
    const causa  = sessionStorage.getItem("causa");
    
    if (tipoPeriodo !== "" && periodo !== "" && chofer !== "") {
        switch (indicador_seleccionado) {
            case 1:
                window.location = "reporte_entregas_efectivas_excel.php?tipoPeriodo="+tipoPeriodo+"&periodo="+periodo+"&chofer="+chofer;
                break;
            case 2:
                window.location = "reporte_rechazo_clientes_excel.php?tipoPeriodo="+tipoPeriodo+"&periodo="+periodo+"&chofer="+chofer+"&causa="+causa;
                break;
            case 3:
                window.location = "reporte_oportunidad_despacho_excel.php?tipoPeriodo="+tipoPeriodo+"&periodo="+periodo+"&chofer="+chofer;
                break;
        }
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    const chofer = sessionStorage.getItem("chofer");
    const tipoPeriodo = sessionStorage.getItem("tipoPeriodo");
    const periodo = sessionStorage.getItem("periodo");
    const causa  = sessionStorage.getItem("causa");

    if (tipoPeriodo !== "" && periodo !== "" && chofer !== "") {
        switch (indicador_seleccionado) {
            case 1:
                window.open('reporte_entregas_efectivas_pdf.php?&tipoPeriodo='+tipoPeriodo+"&periodo="+periodo+"&chofer="+chofer, '_blank');
                break;
            case 2:
                window.open('reporte_rechazo_clientes_pdf.php?&tipoPeriodo='+tipoPeriodo+"&periodo="+periodo+"&chofer="+chofer+"&causa="+causa, '_blank');
                break;
            case 3:
                window.open('reporte_oportunidad_despacho_pdf.php?&tipoPeriodo='+tipoPeriodo+"&periodo="+periodo+"&chofer="+chofer, '_blank');
                break;
        }
    }
});

init();
