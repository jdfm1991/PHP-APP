var tabla_activacionclientes;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    $("#loader").hide();
}

function limpiar() {
    $("#fechaf").val("");
}

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_activacionclientes", function () {
    $("#tabla").hide();
    $("#minimizar").slideToggle(); //MINIMIZAMOS LA TARJETA.
    var fecha_final = $("#fechaf").val();
    if (fecha_final != "") {
		//CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
        tabla_activacionclientes = $('#activacionclientes_data').DataTable({
            "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
            "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
            "ajax": {
                beforeSend: function () {
                    $("#loader").show(''); //MOSTRAMOS EL LOADER.
                },
                url: "activacionclientes_controlador.php?op=buscar_activacionclientes",
                type: "post",
                data: {fecha_final: fecha_final},
                error: function (e) {
                    console.log(e.responseText);
                },
                complete: function () {

                    $("#tabla").show('');//MOSTRAMOS LA TABLA.
                    $("#loader").hide();//OCULTAMOS EL LOADER.
                    //limpiar();//LIMPIAMOS EL SELECTOR.
                }
            },//TRADUCCION DEL DATATABLE.
            "bDestroy": true,
            "responsive": true,
            "bInfo": true,
            "iDisplayLength": 10,
            "order": [[0, "desc"]],
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            },
        });
    }
});


//ACCION AL PRECIONAR EL BOTON.
$(document).on("click","#btn_excel", function(){

	var fecha_final= $("#fechaf").val();

	if(fecha_final !== ""){
		$.ajax({
			url: "activacionclientes_excel.php",
			method: "POST",
			data: {fecha_final: fecha_final},
			beforeSend: function(){
				//
			},
			error: function(e){
				console.log(e.responseText);
			},
			success: function (data) {
				//$('#vehiculo_data').DataTable().ajax.reload();
			}
		});
	}
});

init();
