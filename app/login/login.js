let validator;

$(document).ready(function () {
    $.validator.setDefaults({
        submitHandler: function () {
            guardaryeditar();
        }
    });

    validaciones();
});

function limpiar() {
    validator.resetForm();
}

function guardaryeditar() {
    const formData = new FormData($("#edv_form")[0]);
    $.ajax({
        url: "kpimanager_controlador.php?op=guardar",
        type: "POST",
        dataType: "json",
        data: formData,
        contentType: false,
        processData: false,
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            let { icono, mensaje } = data
            ToastSweetMenssage(icono, mensaje);
            $('#edv_form')[0].reset();
            $('#kpimanagerModal').modal('hide');
            $('#tabla').DataTable().ajax.reload();
            limpiar();
        }
    });
}

function validaciones() {
    validator = $('#login_form').validate({
        rules: {
            login: {
                required: true,
                minlength: 3
            },
            clave: {
                required: true,
                minlength: 5
            },
        },
        messages: {
            login: {
                required: "Campo requerido",
                minlength: "El Campo debe contener al menos 3 caracteres"
            },
            clave: {
                required: "Campo requerido",
                minlength: "El Campo debe contener al menos 5 caracteres"
            }
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
}