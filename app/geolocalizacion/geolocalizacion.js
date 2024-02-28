var tabla;
var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = true;

    listar_rutas();
}


function listar_rutas() {
    let isError = false;
    $.ajax({
        url: "geolocalizacion_controlador.php?op=listar_rutas",
        method: "POST",
        dataType: "json",
        beforeSend: function () {
            SweetAlertLoadingShow();
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if (!jQuery.isEmptyObject(data.listar_rutas)) {
                //lista de seleccion de marcas
                $('#ruta').append('<option name="" value="Todos">Todos</option>');
                $.each(data.listar_rutas, function (idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#ruta').append('<option name="" value="' + opt.CodVend + '">' + opt.CodVend + ':' + opt.Descrip +  '</option>');
                });
            }
        },
        complete: function () {
            if (!isError) SweetAlertLoadingClose();
        }
    });
}

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {

    var ruta = $("#ruta").val();
    var opc = $("#opc").val();
    window.open("geolocalizacion_ventana.php?&vendedores=" + ruta + "&opc=" + opc, "_blank");
});

init();