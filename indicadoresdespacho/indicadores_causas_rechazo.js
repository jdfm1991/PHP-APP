
function rechazo_de_los_clientes(data)
{
    let labels, values, value_max, promedio;

    if(!jQuery.isEmptyObject(data)) {
        /*//titulos de las barras
        labels = data.tabla.map( val => { return val.fecha_entrega; });

        //valores de las barras
        values = data.tabla.map( val => { return parseInt(val.ped_despachados); });

        //obtiene el valor mas alto de los pedidos despachados
        value_max = Math.max(data.tabla.map( val => { return parseInt(val.ped_despachados); }));

        //obtiene un array de valores con el valor promedio
        promedio = values.map(() => { return parseFloat(data.promedio_diario_despacho.replace(',', '.')); });*/
    } else {
        labels = [];
        values = [];
        value_max = 0;
        promedio = 0;
    }

    // retornamos un objeto con el contenido necesario
    // para procesar el grafico
    return {
        labels : labels,
        value_max  : value_max,
        content: [
            {
                label      : 'Despachos',
                type       : 'bar',
                color      : 'rgba(60,141,188,0.8)',
                pointRadius: false,
                fill       : false,
                values     : values
            }, {
                label      : 'Promedio',
                type       : 'line',
                color      : 'rgba(255,99,71,0.9)',
                pointRadius: true,
                fill       : false,
                values     : promedio
            }
        ]
    };
}

function color_causa_rechazo(value)
{
    let hex, rgba;

    switch (value) {
        case "Merc. no solicitada":
            hex = "#7153f9";
            rgba = 'rgba(113, 83, 249, 0.8)';
            break;
        case "Fecha venc. cercana":
            hex = "#4279a7";
            rgba = 'rgba(66, 121, 167, 0.8)';
            break;
        case "EDV no informo mod pago":
            hex = "#c9ea30";
            rgba = 'rgba(201, 234, 48, 0.8)';
            break;
        case "Cliente no puede pagar":
            hex = "#BA9191";
            rgba = 'rgba(186, 145, 145, 0.8)';
            break;
        case "Cliente indisponible":
            hex = "#9ca2a2";
            rgba = 'rgba(156, 162, 162, 0.8)';
            break;
        case "Precio no fue el acordado":
            hex = "#C4CAC8";
            rgba = 'rgba(196, 202, 200, 0.8)';
            break;
        case "Mercancia vencida":
            hex = "#CAB8D4";
            rgba = 'rgba(202, 184, 212, 0.8)';
            break;
        case "Pedido incompleto":
            hex = "#E7E6E3";
            rgba = 'rgba(231, 230, 227, 0.8)';
            break;
        case "Faltante en el almacen":
            hex = "#FAE39F";
            rgba = 'rgba(250, 227, 159, 0.8)';
            break;
        case "Faltante en el bulto":
            hex = "#F2FA9F";
            rgba = 'rgba(242, 250, 159, 0.8)';
            break;
        case "Caja mal estado":
            hex = "#fc9245";
            rgba = 'rgba(252, 146, 69, 0.8)';
            break;
        case "Retraso de entrega":
            hex = "#61D29E";
            rgba = 'rgba(97, 210, 158, 0.8)';
            break;
        case "Facturado bajo cero":
            hex = "#298776";
            rgba = 'rgba(41, 135, 118, 0.8)';
            break;
        case "Otro":
            hex = "#a9d2f5";
            rgba = 'rgba(169, 210, 245, 0.8)';
            break;
    }

    return {
        hex : hex,
        rgba: rgba
    };
}

