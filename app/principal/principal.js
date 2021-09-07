
//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    fetch_pordespachar();
    fetch_porfacturar();
    fetch_cxc();
    fetch_cxp();
    cargar_grafica_ventasXmesdivisas();
    fetch_inventario_valorizado();
    fetch_clientes_naturales();
    fetch_clientes_juridicos();
    fetch_tasa_dolar();
    fetch_devoluciones_sin_motivo();
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
    let isError = false;
    $.ajax({
        cache: false,
        async: false,
        url: "principal/principal_controlador.php?op=buscar_ventasPormesdivisas",
        type: "post",
        dataType: "json",
        beforeSend: function () {
            $('#loader_ventas_por_mes').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            if (!jQuery.isEmptyObject(data)) {
                let { anio, cantidad_meses_evaluar, datos, valor_mas_alto } = data;

                let arr_temp =[];
                for (let i=1;i<=cantidad_meses_evaluar;i++) {
                    arr_temp.push({num_mes:i, mes:'', valor:0});
                }
                console.log(arr_temp);

                /*let labels, values;
                if(!jQuery.isEmptyObject(datos)) {
                    //titulos de las barras
                    labels = datos.map( val => { return val.mes; });

                    //valores de las barras
                    values = datos.map( val => { return parseInt(val.valor); });
                } else {
                    labels = [];
                    values = [];
                }

                graficar(labels, values, (parseInt(valor_mas_alto) * 1.05), 5, '$', $('#sales-chart'), 'line');*/
            } else {
                $('#sales-chart').html('<div class="alert alert-warning">No existe datos para el grafico. </div>');
            }
        },
        complete: function () {
            if(!isError) $('#loader_ventas_por_mes').hide();
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
        beforeSend: function () {
            $('#loader_inventario_valorizado').show()
        },
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
            if(!isError) $('#loader_inventario_valorizado').hide();
        }
    });
}

function fetch_clientes_naturales() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=buscar_clientes_naturales",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_clientes_n').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { cant_naturales } = data;
                $('#clientes_n').text(cant_naturales);
            }
        },
        complete: function () {
            if(!isError) $('#loader_clientes_n').hide();
        }
    });
}

function fetch_clientes_juridicos() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=buscar_clientes_juridicos",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_clientes_j').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { cant_juridico } = data;
                $('#clientes_j').text(cant_juridico);
            }
        },
        complete: function () {
            if(!isError) $('#loader_clientes_j').hide();
        }
    });
}

function fetch_tasa_dolar() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=buscar_tasa_dolar",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_tasa_dolar').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { tasa } = data;
                $('#tasa_dolar').html(tasa + '<sup style="font-size: 16px">$</sup>');
            }
        },
        complete: function () {
            if(!isError) $('#loader_tasa_dolar').hide();
        }
    });
}

function fetch_devoluciones_sin_motivo() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=buscar_devoluciones_sin_motivo",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_devoluciones_sin_motivo').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { devoluciones_sin_motivo } = data;
                $('#devoluciones_sin_motivo').text(devoluciones_sin_motivo);
            }
        },
        complete: function () {
            if(!isError) $('#loader_devoluciones_sin_motivo').hide();
        }
    });
}

init();