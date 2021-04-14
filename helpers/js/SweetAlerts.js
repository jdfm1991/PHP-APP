
function SweetAlertLoadingShow(message = "Cargando, espere...") {
    swal.fire({
        html: '<h5>'+ message + '</h5>',
        showConfirmButton: false,
        allowOutsideClick: false,
        onRender: function() {
            // there will only ever be one sweet alert open.
            $('.swal2-content').prepend(sweet_loader);
        }
    });
}

function SweetAlertSuccessLoading(message = "Carga completa!") {
    swal.fire({
        icon: 'success',
        showConfirmButton: false,
        timer: 1000,
        html: '<h5>'+ message +'</h5>'
    });
}

function SweetAlertLoadingClose() { swal.close() }

function SweetAlertError(message = "",title = "Atención!", icon = "error") {
    Swal.fire(title, message, icon);
}