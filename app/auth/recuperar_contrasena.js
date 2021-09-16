
$(document).ready(function () {

});

function clickBtnSend() {
    if ($('#email').val().length > 3) {

        $.ajax({
            url: "auth/auth_controlador.php?op=generate_code_recovery_user",
            type: "POST",
            dataType: "json",
            data: {user: $('#email').val()},
            beforeSend: function () {
                $('#email').prop('disabled', 'disabled');
            },
            error: function (e) {
                SweetAlertError(e.responseText, "Error!")
                console.log(e.responseText);
            },
            success: function (callback) {
                let {  status, message } = callback

                if (status) {
                    $("#errorRecover").hide();
                    $('#div_codigo').html(input_codigo);
                    $('#mensajeRecover').html('');
                    $('#btnSend').hide();
                    $('#btnValidar').show();
                } else {
                    $("#errorRecover").show();
                    $('#btnSend').show();
                    $('#mensajeRecover').html(message);
                    $('#email').prop('disabled', '');
                }
            }
        });
    } else {
        $('#email').prop('disabled', '');
        $('#div_codigo').html('');
        $("#errorRecover").show();
        $('#mensajeRecover').html('Ingrese nombre de usuario !');
    }
}

function limpiarRecover() {
    $('#loginModal').modal('hide')
    $("#errorRecover").hide();
    $("#mensajeRecover").html('');
    $('#email').prop('disabled', '');
    $('#email').val('');
    $('#div_codigo').html('');
    $('#div_clave1').html('');
    $('#div_clave2').html('');
}

function login() {
    let user = $('#login').val()
    let clave = $('#clave').val()
    // const formData = new FormData($("#login_form")[0]);
    $.ajax({
        url: "auth/auth_controlador.php?op=login_in",
        type: "POST",
        dataType: "json",
        data: {login:user, clave:clave},
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (callback) {
            let {  status, message, data } = callback

            if (status) {
                $("#error").hide();
                $('#login_form')[0].reset();
                $('#loginModal').modal('hide');
                limpiar();

                window.location = 'principal.php'
            } else {
                $("#error").show();
                $('#mensaje').html(message);
            }

        }
    });
}

let input_codigo = () => {
    return `<div class="input-group-prepend">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
            <input id="codigo" name="codigo" type="text" class="form-control" placeholder="Codigo de seguridad enviado al correo"> `;
}

let input_clave1 = () => {
    return `<div class="input-group-prepend">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
            <input id="clave1" name="clave1" type="password" class="form-control" placeholder="Nueva Contraseña"> `;
}

let input_clave2 = () => {
    return `<div class="input-group-prepend">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
            <input id="clave2" name="clave2" type="password" class="form-control" placeholder="Repetir Nueva Contraseña"> `;
}