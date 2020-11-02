var estado_minimizado;

// 1-efectivas  2-rechazo  3-oportunidad
var indicador_seleccionado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    $('#grafico').hide();
    $("#loader").hide();
    estado_minimizado = false;
    indicador_seleccionado = 1;
    listar_choferes();
    // listar_causas_de_rechazo(); PENDIENTE TERMINAR
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
    $("#chofer").val("");
    $("#causa").val("");
}

function validarCantidadRegistrosTabla(data) {
    (data.length === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function () {
    ($("#fechai").val() !== "" && $("#fechaf").val() !== "" && $("#chofer").val() !== "" /*&& $("#causa").val() !== ""*/)
        ? estado_minimizado = true : estado_minimizado = false;
};

$(document).ready(function(){
    $("#fechai").change(() => no_puede_estar_vacio());
    $("#fechaf").change(() => no_puede_estar_vacio());
    $("#chofer").change(() => no_puede_estar_vacio());

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

        let array_selects = ['pills-efectivas', 'pills-rechazo', 'pills-oportunidad'];

        array_selects.forEach( pill => {

            //lista de seleccion de choferes
            $('#'+pill+' #chofer').append('<option name="" value="">Seleccione</option>');
            $('#'+pill+' #chofer').append('<option name="" value="16395823" selected>prueba</option>');
            $.each(data.lista_choferes, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#'+pill+' #chofer').append('<option name="" value="' + opt.Cedula +'">' + opt.Nomper + '</option>');
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
            listar = "";
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
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if ((formData[0]['name'] === "fechai" && formData[0]['value'] !== "")
            && (formData[1]['name'] === "fechaf" && formData[1]['value'] !== "")
            && (formData[2]['name'] === "chofer" && formData[2]['value'] !== "")
            /*&& $("#causa").val() !== ""*/
        ) {
            sesionStorageItems(formData[0]['value'], formData[1]['value'], formData[2]['value'] /*, $("#causa").val()*/);

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

                    //proceso de llenado del grafico
                    construirGrafico(data, title);

                    //proceso de llenado de la tabla
                    if(!jQuery.isEmptyObject(data)) {
                        $.each(data.tabla, function(idx, opt) {
                            $('#indicadores_data')
                                .append(
                                    '<tr>' +
                                    '<td align="center" class="small align-middle">' + opt.num + '</td>' +
                                    '<td align="center" class="small align-middle">' + opt.fecha_entrega + '</td>' +
                                    '<td align="center" class="small align-middle">' + opt.ped_despachados + '</td>' +
                                    '<td align="center" class="small align-middle">' + opt.porc_efectividad + '</td>' +
                                    '<td align="center" class="small align-middle">' + opt.ordenes_despacho + '</td>' +
                                    '</tr>'
                                );
                        });
                    }

                    $("#loader").hide();//OCULTAMOS EL LOADER.
                    validarCantidadRegistrosTabla(data.tabla);
                    //limpiar();//LIMPIAMOS EL SELECTOR.

                }
            });

            /*tabla = $('#indicadores_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                processData: false,
                contentType: false,
                "ajax": {
                    beforeSend: function () {
                        $("#loader").show(''); //MOSTRAMOS EL LOADER.
                    },
                    url: "indicadoresdespacho_controlador.php?op="+listar,
                    type: "post",
                    data: function(d) {
                        $.each(formData, function(key, val) {
                            d[val.name] = val.value;
                        });
                    },
                    error: function (e) {
                        console.log(e.responseText);
                    },
                    complete: function () {

                        $("#tabla").show('');//MOSTRAMOS LA TABLA.
                        $("#grafico").show('');//MOSTRAMOS EL GRAFICO.

                        var areaChartData = {
                            labels  : ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                            datasets: [
                                {
                                    label               : 'Digital Goods',
                                    backgroundColor     : 'rgba(60,141,188,0.9)',
                                    borderColor         : 'rgba(60,141,188,0.8)',
                                    pointRadius          : false,
                                    pointColor          : '#3b8bba',
                                    pointStrokeColor    : 'rgba(60,141,188,1)',
                                    pointHighlightFill  : '#fff',
                                    pointHighlightStroke: 'rgba(60,141,188,1)',
                                    data                : [28, 48, 40, 19, 86, 27, 90]
                                },
                                {
                                    label               : 'Electronics',
                                    backgroundColor     : 'rgba(210, 214, 222, 1)',
                                    borderColor         : 'rgba(210, 214, 222, 1)',
                                    pointRadius         : false,
                                    pointColor          : 'rgba(210, 214, 222, 1)',
                                    pointStrokeColor    : '#c1c7d1',
                                    pointHighlightFill  : '#fff',
                                    pointHighlightStroke: 'rgba(220,220,220,1)',
                                    data                : [65, 59, 80, 81, 56, 55, 40]
                                },
                            ]
                        }
                        var barChartCanvas = $('#barChart').get(0).getContext('2d')
                        var barChartData = jQuery.extend(true, {}, areaChartData)

                        var temp0 = areaChartData.datasets[0]
                        var temp1 = areaChartData.datasets[1]
                        barChartData.datasets[0] = temp1
                        barChartData.datasets[1] = temp0

                        var barChartOptions = {
                            responsive              : true,
                            maintainAspectRatio     : false,
                            datasetFill             : false
                        }

                        var barChart = new Chart(barChartCanvas, {
                            type: 'bar',
                            data: barChartData,
                            options: barChartOptions
                        })

                        $("#loader").hide();//OCULTAMOS EL LOADER.
                        $(".title-card").text(title);
                        validarCantidadRegistrosTabla();
                        limpiar();//LIMPIAMOS EL SELECTOR.

                    }
                },//TRADUCCION DEL DATATABLE.
                "bDestroy": true,
                "responsive": true,
                "bInfo": true,
                "iDisplayLength": 10,
                "order": [[0, "desc"]],
                /!*'columnDefs' : [{
                    'visible': false, 'targets': [0]
                }],*!/
                "language": texto_español_datatables
            });*/
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
    }
});

function sesionStorageItems(fechai, fechaf, chofer, causa = ""){
    sessionStorage.setItem("fechai", fechai);
    sessionStorage.setItem("fechaf", fechaf);
    sessionStorage.setItem("chofer", chofer);
    sessionStorage.setItem("causa", causa);
}

function construirGrafico(data, title) {
    let labels, values, value_max;

    if(!jQuery.isEmptyObject(data)) {
        //titulos de las barras
        labels = data.tabla.map( val => { return val.fecha_entrega; });

        //valores de las barras
        values = data.tabla.map( val => { return parseInt(val.ped_despachados); });

        //obtiene el valor mas alto de los pedidos despachados
        value_max = Math.max(data.tabla.map( val => { return parseInt(val.ped_despachados); }));
    } else {
        labels = [];
        values = [];
        value_max = 0;
    }

    //CONSTRUCCION DEL GRAFICO
    const barChart = new Chart($('#barChart').get(0).getContext('2d'), {
        type: 'bar',
        data: jQuery.extend(true, {}, {
            labels  : labels,
            datasets: [{
                label               : title,
                backgroundColor     : 'rgba(60,141,188,0.9)',
                borderColor         : 'rgba(60,141,188,0.8)',
                pointRadius         : false,
                pointColor          : '#3b8bba',
                pointStrokeColor    : 'rgba(60,141,188,1)',
                pointHighlightFill  : '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                data                : values
            }]
        }),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            datasetFill: false,
            scales: {
                yAxes: [{
                    ticks: {
                        suggestedMin: 0,
                        suggestedMax: (value_max > 50) ? value_max : 50
                    }
                }],
            },
        }
    });
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
