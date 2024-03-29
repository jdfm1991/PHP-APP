$(document).ready(function () {
    rate = $("#factor_nuevo").val();
    //console.log(rate)
    if (rate == '0,00' ) {
        $('#btn_guardar').attr('disabled', true);
    }

    $('#factor_nuevo').keyup(function (e) { 
        rate = $(this).val(); 
        console.log(rate)
        if (rate != '0,00' ) {
            $('#btn_guardar').attr('disabled', false);
        }else{
            $('#btn_guardar').attr('disabled', true);
        }
        
    });
    
});

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {

    getFactorActual();

    $('#factor_nuevo').maskMoney({
        allowNegative: false,
        thousands:'.',
        decimal:',',
        affixesStay: false,
        allowZero: true
    });

}

function limpiar() {
    $("#factor_nuevo").val("0,00");
}

function getFactorActual(){
    $.ajax({
        url: "factorcambiario_controlador.php?op=mostrar_factor",
        type: "POST",
        dataType: "json",
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            console.log(data)
            if(!jQuery.isEmptyObject(data)) {
                let { factor } = data;

                $('#factor_activo').val(factor);
            }
        }
    });
}

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_guardar", function () {

    const formData = new FormData($("#frm_factor")[0]);

    $.ajax({
        async: true,
        url: "factorcambiario_controlador.php?op=guardaryeditar",
        method: "POST",
        dataType: "json",
        data: formData,
        contentType: false,
        processData: false,
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)) {
                let { icono, mensaje } = data
                ToastSweetMenssage(icono, mensaje);

                if (icono !=='error') {
                    limpiar();
                    getFactorActual();
                }
            }

        }
    });
});

$(document).on("click", "#syncrate", function () {
    //$('#Detalles_ventas_d').modal('show');
    $.ajax({
        cache: true,
        url: "factorcambiario_controlador.php?op=guardaryeditar",
         method: "POST",
        dataType: "json",
        contentType: false,
        processData: false,
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)) {
                let { icono, mensaje } = data
                ToastSweetMenssage(icono, mensaje);

                if (icono !=='error') {
                    limpiar();
                    getFactorActual();
                }
            }

        }
    });
 });

init();