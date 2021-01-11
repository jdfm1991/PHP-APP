
function oportunidad_despacho(data)
{
    let labels, values, value_max, promedio;

    /* if(!jQuery.isEmptyObject(data)) {
        //titulos de las barras
        labels = data.tabla.map( val => { return val.fecha_entrega; });

        //valores de las barras
        values = data.tabla.map( val => { return parseInt(val.cant_documentos); });

        //obtiene el valor mas alto de los pedidos despachados
        value_max = Math.max(data.tabla.map( val => { return parseInt(val.ped_despachados); }));

        //obtiene un array de valores con el valor promedio
        promedio = values.map(() => { return parseFloat(data.promedio_diario_despacho.replace(',', '.')); });
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
    }; */
}

function construirTablaOportunidadDespacho(data) {

    tabla = $('#oportunidad_data').DataTable({
        "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
        "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
        "sEcho": data.sEcho, //INFORMACION PARA EL DATATABLE
        "iTotalRecords": data.iTotalRecords, //TOTAL DE REGISTROS AL DATATABLE.
        "iTotalDisplayRecords": data.iTotalDisplayRecords, //TOTAL DE REGISTROS A VISUALIZAR.
        "data": data.aaData, // informacion por registro
        "bDestroy": true,
        "responsive": false,
        "bInfo": true,
        "iDisplayLength": 10,
        // "order": [[0, "desc"]],
        'columnDefs':[{
            "targets": [0,1,2,3,4,5,6,7], // your case first column
            "className": "text-center"
        }],
        "language": texto_español_datatables
    });

}

function thead_table_oportunidad()
{
    return '' +
        '<tr>' +
        '<th align="center" class="align-middle">Nro Fact</th>' +
        '<th align="center" class="align-middle">Ruta</th>' +
        '<th align="center" class="align-middle">Cliente</th>' +
        '<th align="center" class="align-middle">Fecha Despacho</th>' +
        '<th align="center" class="align-middle">Fecha Recibe Cliente</th>' +
        '<th align="center" class="align-middle">% Oportunidad</th>' +
        '</tr>'
}