
//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    listar_librocompras();
}

$(document).ready(function(){

});

function listar_librocompras(){

    let fechai    = $('#fechai').val();
    let fechaf    = $('#fechaf').val();

    $.ajax({
        url: "librocompras_controlador.php?op=listar_librocompras",
        method: "POST",
        data: {fechai: fechai, fechaf: fechaf},
        dataType: "json", // Formato de datos que se espera en la respuesta
        beforeSend: function () {
            SweetAlertLoadingShow("Procesando información, espere...");
        },
        error: function (e) {
            SweetAlertError(e.responseText.substring(0, 400) + "...", "Error!")
            console.log(e.responseText);
        },
        success: function (datos) {
            /*if(!jQuery.isEmptyObject(datos.tabla)){
                $.each(datos.tabla, function(idx, opt) {
                    let { coordinador, data, subtotal } = opt

                    $('#tabla').append('<tr><td class="text-left" colspan="'+colspanTotal+'">Coordinador:   <strong>' + coordinador.toUpperCase() + '</strong></td></tr>');

                    $.each(data, function(idx, opt) {
                        $('#tabla').append(obtenerInfoTabla(opt));
                    });

                    $('#tabla').append(obtenerInfoTabla(subtotal, true, true));
                });
            }
            $('#tabla').append('<tr><td colspan="'+colspanTotal+'">'+ "" +'</td></tr>');
            $('#tabla').append(obtenerInfoTabla(datos.total_general, true, true));*/
        },
        complete: function () {
            SweetAlertSuccessLoading()

            $('#tabla').columntoggle({
                //Class of column toggle contains toggle link
                toggleContainerClass:'columntoggle-container',
                //Text in column toggle box
                toggleLabel:'MOSTRAR/OCULTAR CELDAS: ',
                //the prefix of key in localstorage
                keyPrefix:'columntoggle-',
                //keyname in localstorage, if empty, it will get from URL
                key:''

            });
        }
    });
}