
var tabla;

//Función que se ejecuta al inicio
function init(){
	listar();

      //cuando se da click al boton submit entonces se ejecuta la funcion guardaryeditar(e);
      $("#rol_form").on("submit",function(e)
      {
      	guardaryeditar(e);
      })

	 //cambia el titulo de la ventana modal cuando se da click al boton
	 $("#add_button").click(function(){

		     //habilita los campos cuando se agrega un registro nuevo ya que cuando se editaba un registro asociado entonces aparecia deshabilitado los campos
		     $("#rol").attr('disabled', false);


		     $(".modal-title").text("Agregar Rol");

		 });
	}

	/*funcion para limpiar formulario de modal*/
	function limpiar(){
		$("#rol").val("");

		$('#id_rol').val("");
	}

//function listar

function listar(){
	tabla=$('#roles_data').dataTable({

"aProcessing": true,//Activamos el procesamiento del datatables
"aServerSide": true,//Paginación y filtrado realizados por el servidor
"ajax":
{
	url: 'roles_controlador.php?op=listar',
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



function mostrar(id_rol){

	$.post("roles_controlador.php?op=mostrar",{id_rol : id_rol}, function(data, status)

	{

		data = JSON.parse(data);

                 //si existe la cedula_relacion entonces tiene relacion con otras tablas
                 if(data.cedula_relacion){

                 	$('#rolModal').modal('show');
                 	$('#rol').val(data.cedula_relacion);
						//desactiva el campo

						$('#rol').val(data.descripcion);
						$("#rol").prop("disabled", false);

						$('.modal-title').text("Editar Rol");
						$('#id_rol').val(id_rol);

					} else{

						$('#rolModal').modal('show');
						$('#rol').val(data.descripcion);
						$("#rol").prop("disabled", false);

						$('.modal-title').text("Editar Rol");
						$('#id_rol').val(id_rol);
					}
				});


}

//la funcion guardaryeditar(e); se llama cuando se da click al boton submit

function guardaryeditar(e){

      	e.preventDefault(); //No se activará la acción predeterminada del evento
      	var formData = new FormData($("#rol_form")[0]);

      	$.ajax({

      		url: "roles_controlador.php?op=guardaryeditar",
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

      			$('#rol_form')[0].reset();
      			$('#rolModal').modal('hide');
      			$('#roles_data').DataTable().ajax.reload();
      			limpiar();
      		}
      	});


      }

//Mostrar datos del usuario en la ventana modal del formularioS
init();






