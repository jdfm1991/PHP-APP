
//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    fetch_pordespachar();
    fetch_porfacturar();
    fetch_cxc()
    fetch_cxp()
    cargar_grafica_ventasXmesdivisas()
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
        type: "get",
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

                graficar(labels, values, (parseInt(valor_mas_alto) * 1.05), 5, '$', '.ventasmesdivisas', 'line');
            } else {
                $('.amp-pxl').html('<div class="alert alert-warning">No existe datos para el grafico. </div>');
            }
        }
    });
}

init();