
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