
//Función que se ejecuta al inicio
function init() {
    listar_permisos_por_rol();
    //cuando se da click al boton submit entonces se ejecuta la funcion guardaryeditar(e);
    $("#rol_form").on("submit", function (e) {
        guardaryeditar(e);
    })

    //cambia el titulo de la ventana modal cuando se da click al boton
    $("#btnGestion").click(function () {
        window.location = "../gestionsistema/gestionsistema.php";
    });
}

function listar_permisos_por_rol() {
    let isError = false;
    let id = $("#id").val();
    $.ajax({
        async: true,
        cache: true,
        url: 'permisos_controlador.php?op=listar_permisos_por_rol',
        method: "POST",
        dataType: "json",
        data: {rol_id: id},
        beforeSend: function () {
            SweetAlertLoadingShow();
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {

            if(!jQuery.isEmptyObject(data)){
                /*$.each(data, function(idx, opt) {
                    let { menu_id, menu_nombre, modulos } = opt;

                    $('#permisos').append('<h4 id="menu_'+menu_id+'" class="card-title m-t-20">'+ menu_nombre +'</h4>');

                    let temp='<div class="row demo-swtich">';
                    if (!jQuery.isEmptyObject(modulos)) {
                        $.each(modulos, function(idx, opt) {
                            let { id, nombre, selected } = opt;
                            temp += '<div class="col-6">' +
                                '<div class="row">' +
                                '<div class="demo-switch-title col-8">' + menu_nombre + ' --> ' + nombre + '</div>' +
                                '<div class="switch col-3">' +
                                '<label>' +
                                '<input id="modulo_'+id+'" onchange="guardar(\''+ id +'\')" type="checkbox" '+ (selected ? 'checked':'') +'>' +
                                '<span class="lever switch-col-light-blue"></span>' +
                                '</label>' +
                                '</div>' +
                                '</div>' +
                                '</div>';
                        });
                    } else {
                        temp += '<div class="col-12">' +
                            '<div class="demo-switch-title">' +
                            '<span class="label label-warning">Sin Permisos para este menú</span>' +
                            '</div>' +
                            '</div>';
                    }
                    temp += '</div>';
                    $('#permisos').append(temp);
                });*/
            }
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

function guardar(modulo_id) {
    let rol_id = $("#id").val();
    let state = document.getElementById('modulo_'+modulo_id).checked === true;
    $.ajax({
        url: `${baseUrl}permisos/guardarrolmod`,
        type: "POST",
        dataType: "json",
        data: {modulo_id: modulo_id, rol_id: rol_id, state: state},
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            let { icono, mensaje } = data;
            ToastSweetMenssage(icono, mensaje);
        }
    });
}

//Mostrar datos del usuario en la ventana modal del formularioS
init();