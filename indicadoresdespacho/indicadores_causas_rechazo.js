
function rechazo_de_los_clientes(data, condicion_visibilidad_mes)
{
    let labels, value_max, values;

    if(!jQuery.isEmptyObject(data)) {
        //titulos de las barras
        labels = data.tabla.map( val => { return condicion_visibilidad_mes ? val.nombre_mes : val.fecha_entrega; });

        //creamos un array con todas las posiciones en 0 de la cantidad de observaciones
        arr_temp = data.tabla.map( val => { return val.observacion.length; });
        index_max_obs = arr_temp.findIndex( val => { val === Math.max(...arr_temp); });
        values = data.tabla[index_max_obs].observacion.map(() => { return 0 });

        //obtiene el valor mas alto de los pedidos despachados
        value_max = Math.max(...data.tabla.map( val => { return parseInt(val.cant_documentos); }));
    } else {
        labels = [];
        values = [];
        value_max = 0;
    }

    // retornamos un objeto con el contenido necesario
    // para procesar el grafico
    return {
        labels : labels,
        value_max  : value_max,
        content: [
            ...data.tabla.map( (val, index) => {

                const value = values.slice();
                value[index] = parseInt(val.cant_documentos)

                return {
                    label      : /*val.observacion*/"merc. no solicitada",
                    type       : 'bar',
                    color      : color_causa_rechazo(/*val.observacion*/"merc. no solicitada").rgba,
                    pointRadius: false,
                    fill       : false,
                    values     : value
                };
            })
        ]
    };
}

function thead_table_rechazo(incluye_ordenes)
{
    thead_ordenes = (!incluye_ordenes) ? '<th align="center" class="align-middle">Orden(es) Despacho</th>' : '';

    return '' +
        '<tr>' +
        '<th align="center"  class="align-middle">Fecha Devolución</th>' +
        '<th align="center"  class="align-middle">Devoluciones</th>' +
        '<th align="center"  class="align-middle">% Rechazos</th>' +
        thead_ordenes +
        '</tr>'
}

function color_causa_rechazo(value)
{
    let hex, rgba;

    switch (value.toLowerCase()) {
        case "merc. no solicitada":
            hex = "#7153f9";
            rgba = 'rgba(113, 83, 249, 0.8)';
            break;
        case "fecha venc. cercana":
            hex = "#4279a7";
            rgba = 'rgba(66, 121, 167, 0.8)';
            break;
        case "edv no informo mod pago":
            hex = "#c9ea30";
            rgba = 'rgba(201, 234, 48, 0.8)';
            break;
        case "cliente no puede pagar":
            hex = "#BA9191";
            rgba = 'rgba(186, 145, 145, 0.8)';
            break;
        case "cliente indisponible para recepcion":
            hex = "#9ca2a2";
            rgba = 'rgba(156, 162, 162, 0.8)';
            break;
        case "precio no fue el acordado":
            hex = "#C4CAC8";
            rgba = 'rgba(196, 202, 200, 0.8)';
            break;
        case "mercancia vencida":
            hex = "#CAB8D4";
            rgba = 'rgba(202, 184, 212, 0.8)';
            break;
        case "pedido incompleto":
            hex = "#E7E6E3";
            rgba = 'rgba(231, 230, 227, 0.8)';
            break;
        case "faltante en el almacen":
            hex = "#FAE39F";
            rgba = 'rgba(250, 227, 159, 0.8)';
            break;
        case "faltante en el bulto":
            hex = "#F2FA9F";
            rgba = 'rgba(242, 250, 159, 0.8)';
            break;
        case "caja mal estado":
            hex = "#fc9245";
            rgba = 'rgba(252, 146, 69, 0.8)';
            break;
        case "retraso de entrega":
            hex = "#61D29E";
            rgba = 'rgba(97, 210, 158, 0.8)';
            break;
        case "facturado bajo cero":
            hex = "#298776";
            rgba = 'rgba(41, 135, 118, 0.8)';
            break;
        case "otro":
            hex = "#a9d2f5";
            rgba = 'rgba(169, 210, 245, 0.8)';
            break;
        default:
            hex = "#a9d2f5";
            rgba = 'rgba(169, 210, 245, 0.8)';
            break;
    }

    return {
        hex : hex,
        rgba: rgba
    };
}

