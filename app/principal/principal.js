
//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    fetch_pordespachar();
    fetch_porfacturar();
    fetch_cxc();
    fetch_cxp();
    cargar_grafica_ventasXmesdivisas();
    fetch_inventario_valorizado();
}

function fetch_pordespachar() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=buscar_documentos_pordespachar",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_docPorDespachar').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { por_despachar } = data;
                $('#docPorDespachar').text(por_despachar);
            }
        },
        complete: function () {
            if(!isError) $('#loader_docPorDespachar').hide();
        }
    });
}

function fetch_porfacturar() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=buscar_pedidos_porfacturar",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_pedPorFacturar').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { por_facturar } = data;
                $('#pedsPorFacturar').text(por_facturar);
            }
        },
        complete: function () {
            if(!isError) $('#loader_pedPorFacturar').hide();
        }
    });
}

function fetch_cxc() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=buscar_cxc",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_cxc').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { cxc_bs, cxc_$ } = data;
                $('#cxc_in_dolar').text(cxc_$);
                $('#cxc_in_bs').text(cxc_bs);
            }
        },
        complete: function () {
            if(!isError) $('#loader_cxc').hide();
        }
    });
}

function fetch_cxp() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=buscar_cxp",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_cxp').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { cxp_bs, cxp_$ } = data;
                $('#cxp_in_dolar').text(cxp_$);
                $('#cxp_in_bs').text(cxp_bs);
            }
        },
        complete: function () {
            if(!isError) $('#loader_cxp').hide();
        }
    });
}

function cargar_grafica_ventasXmesdivisas() {
    $.ajax({
        cache: false,
        async: false,
        url: "principal/principal_controlador.php?op=buscar_ventasPormesdivisas",
        type: "post",
        dataType: "json",
        error: function (e) {
            console.log(e.responseText);
        },
        success: function (data) {
            if (!jQuery.isEmptyObject(data)) {
                let { anio, datos, valor_mas_alto } = data;

                let labels, values;
                if(!jQuery.isEmptyObject(datos)) {
                    //titulos de las barras
                    labels = datos.map( val => { return val.mes; });

                    //valores de las barras
                    values = datos.map( val => { return parseInt(val.valor); });
                } else {
                    labels = [];
                    values = [];
                }

                // graficar(labels, values, (parseInt(valor_mas_alto) * 1.05), 5, '$', '#sales-chart', 'line');

                const ticksStyle = {
                    fontColor: '#495057',
                    fontStyle: 'bold'
                };
                const mode = 'index';
                const intersect = true;
                var visitorsChart  = new Chart('#sales-chart', {
                    data   : {
                        labels  : ['18th', '20th', '22nd', '24th', '26th', '28th', '30th'],
                        datasets: [{
                            type                : 'line',
                            data                : [100, 120, 170, 167, 180, 177, 160],
                            backgroundColor     : 'transparent',
                            borderColor         : '#007bff',
                            pointBorderColor    : '#007bff',
                            pointBackgroundColor: '#007bff',
                            fill                : false
                            // pointHoverBackgroundColor: '#007bff',
                            // pointHoverBorderColor    : '#007bff'
                        },
                            {
                                type                : 'line',
                                data                : [60, 80, 70, 67, 80, 77, 100],
                                backgroundColor     : 'tansparent',
                                borderColor         : '#ced4da',
                                pointBorderColor    : '#ced4da',
                                pointBackgroundColor: '#ced4da',
                                fill                : false
                                // pointHoverBackgroundColor: '#ced4da',
                                // pointHoverBorderColor    : '#ced4da'
                            }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        tooltips           : {
                            mode     : mode,
                            intersect: intersect
                        },
                        hover              : {
                            mode     : mode,
                            intersect: intersect
                        },
                        legend             : {
                            display: false
                        },
                        scales             : {
                            yAxes: [{
                                // display: false,
                                gridLines: {
                                    display      : true,
                                    lineWidth    : '4px',
                                    color        : 'rgba(0, 0, 0, .2)',
                                    zeroLineColor: 'transparent'
                                },
                                ticks    : $.extend({
                                    beginAtZero : true,
                                    suggestedMax: 200
                                }, ticksStyle)
                            }],
                            xAxes: [{
                                display  : true,
                                gridLines: {
                                    display: false
                                },
                                ticks    : ticksStyle
                            }]
                        }
                    }
                })

            } else {
                $('#sales-chart').html('<div class="alert alert-warning">No existe datos para el grafico. </div>');
            }
        }
    });
}

function fetch_inventario_valorizado() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=listar_inventario_valorizado",
        method: "get",
        dataType: "json",
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { contenido_tabla } = data;
                $.each(contenido_tabla, function (idx, opt) {
                    $('#inventario_valorizado')
                        .append(
                            '<tr>' +
                                '<td class="align-middle">' + opt.almacen + '</td>' +
                                '<td class="align-middle">' + opt.total + ' $</td>' +
                                '<td class="align-middle">' + opt.acciones + '</td>' +
                            '</tr>'
                        );
                });
            } else {
                //en caso de consulta vacia, mostramos un mensaje de vacio
                $('#inventario_valorizado').append('<tr><td colspan="3" align="center">Sin registros para esta Consulta</td></tr>');
            }
        },
        complete: function () {
            // if(!isError) $('#loader_cxp').hide();
        }
    });
}

init();