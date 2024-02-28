var tabla;

//Función que se ejecuta al inicio
function init() {
    listar();
}


//function listar
function listar() {
    let isError = false;
    tabla = $('#Data_Entry_Vendedores').dataTable({

        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax": {
            url: 'DataVendedores_controlador.php?op=listar',
            type: "get",
            dataType: "json",
            beforeSend: function () {
                SweetAlertLoadingShow();
            },
            error: function (e) {
                isError = SweetAlertError(e.responseText, "Error!")
                send_notification_error(e.responseText);
                console.log(e.responseText);
            },
            complete: function () {
                if (!isError) SweetAlertLoadingClose();
                $("#tabla").show('');//MOSTRAMOS LA TABLA.
            }
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]],
        "language": texto_español_datatables
    }).DataTable();
}


function cambiar(CodVend, valor, id) {

    var input = '';
    input = '#input' + (id);
    var valorCampo = $(input).val();

           $.ajax({
                url: "DataVendedores_controlador.php?op=guardaryeditar",
                method: "POST",
               data: { CodVend: CodVend, valorCampo: valorCampo },
                error: function (e) {
                    SweetAlertError(e.responseText, "Error!")
                    send_notification_error(e.responseText);
                    console.log(e.responseText);
                },
                success: function (data) {
                    SweetAlertSuccessLoading(data.mensaje);
                    $('#Data_Entry_Vendedores').DataTable().ajax.reload();
                }
            });
        
}


init();