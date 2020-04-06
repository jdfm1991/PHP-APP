
var tabla;

//Función que se ejecuta al inicio
function init(){
	listar();

      //cuando se da click al boton submit entonces se ejecuta la funcion guardaryeditar(e);
      $("#chofer_form").on("submit",function(e)
      {
      	guardaryeditar(e);
      })

	 //cambia el titulo de la ventana modal cuando se da click al boton
	 $("#add_button").click(function(){

		     //habilita los campos cuando se agrega un registro nuevo ya que cuando se editaba un registro asociado entonces aparecia deshabilitado los campos
		     $("#cedula").attr('disabled', false);
		     $("#nomper").attr('disabled', false);

		     $(".modal-title").text("Agregar Chofer");

		 });
	}

	/*funcion para limpiar formulario de modal*/
	function limpiar(){
		$("#cedula").val("");
		$('#nomper').val("");
		$('#estado').val("");
		$('#id_chofer').val("");
	}

//function listar

function listar(){
	tabla=$('#choferes_data').dataTable({

"aProcessing": true,//Activamos el procesamiento del datatables
"aServerSide": true,//Paginación y filtrado realizados por el servidor
"ajax":
{
	url: 'chofer_controlador.php?op=listar',
	type : "get",
	dataType : "json",
	error: function(e){
		console.log(e.responseText);
	}
},

"bDestroy": true,
"responsive": true,
"bInfo":true,
"iDisplayLength": 10,//Por cada 10 registros hace una paginación
"order": [[ 0, "asc" ]],//Ordenar (columna,orden)
"language": {
	"sProcessing":     "Procesando...",
	"sLengthMenu":     "Mostrar _MENU_ registros",
	"sZeroRecords":    "No se encontraron resultados",
	"sEmptyTable":     "Ningún dato disponible en esta tabla",
	"sInfo":           "Mostrando un total de _TOTAL_ registros",
	"sInfoEmpty":      "Mostrando un total de 0 registros",
	"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
	"sInfoPostFix":    "",
	"sSearch":         "Buscar:",
	"sUrl":            "",
	"sInfoThousands":  ",",
	"sLoadingRecords": "Cargando...",
	"oPaginate": {
		"sFirst":    "Primero",
		"sLast":     "Último",
		"sNext":     "Siguiente",
		"sPrevious": "Anterior"
	},
	"oAria": {
		"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
		"sSortDescending": ": Activar para ordenar la columna de manera descendente"
	}
}//cerrando language

}).DataTable();
}

function cambiarEstado(id,est){

	Swal.fire({
		title: '¿Estas Seguro?',
		text: "¿De realizar el cambio de estado?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si, cambiar!',
		cancelButtonText: 'Cancelar'
	}).then((result) => {
		if (result.value) {
			$.ajax({
				url:"chofer_controlador.php?op=activarydesactivar",
				method:"POST",
				data:{id:id, est:est},
				success: function(data){
					$('#choferes_data').DataTable().ajax.reload();
				}
			});
		}
	})
}

function mostrar(id_chofer){

	$.post("chofer_controlador.php?op=mostrar",{id_chofer : id_chofer}, function(data, status)

	{

		data = JSON.parse(data);

                 //si existe la cedula_relacion entonces tiene relacion con otras tablas
                 if(data.cedula_relacion){

                 	$('#choferModal').modal('show');
                 	$('#cedula').val(data.cedula_relacion);
						//desactiva el campo

						$('#cedula').val(data.cedula);
						$("#cedula").prop("disabled", true);
						$('#nomper').val(data.nomper);
						$("#nomper").prop("disabled", false);
						$('#estado').val(data.estado);
						$('.modal-title').text("Editar Chofer");
						$('#id_chofer').val(id_chofer);

					} else{

						$('#choferModal').modal('show');
						$('#cedula').val(data.cedula);
						$("#cedula").prop("disabled", true);
						$('#nomper').val(data.nomper);
						$("#nomper").prop("disabled", false);
						$('#estado').val(data.estado);
						$('.modal-title').text("Editar Chofer");
						$('#id_chofer').val(id_chofer);
					}
				});


}

//la funcion guardaryeditar(e); se llama cuando se da click al boton submit

function guardaryeditar(e){

      	e.preventDefault(); //No se activará la acción predeterminada del evento
      	var formData = new FormData($("#chofer_form")[0]);

      	$.ajax({

      		url: "chofer_controlador.php?op=guardaryeditar",
      		type: "POST",
      		data: formData,
      		contentType: false,
      		processData: false,

      		success: function(datos){

      			console.log(datos);

      			const Toast = Swal.mixin({
      				toast: true,
      				position: 'top-end',
      				showConfirmButton: false,
      				timer: 3000,
      				timerProgressBar: true,
      			})

      			Toast.fire({
      				icon: 'success',
      				title: 'Proceso Exitoso!'
      			})

      			$('#chofer_form')[0].reset();
      			$('#choferModal').modal('hide');
      			$('#choferes_data').DataTable().ajax.reload();
      			limpiar();
      		}
      	});


      }

//Mostrar datos del usuario en la ventana modal del formularioS
init();






