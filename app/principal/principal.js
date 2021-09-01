
//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    listar_pordespachar();
}

function limpiar() {
    // $("#fechai").val("");
    // $("#fechaf").val("");
    // $("#marca").val("");
}

function listar_pordespachar() {
    let isError = false;
    $.ajax({
        url: "principal/principal_controlador.php?op=buscar_documentos_pordespachar",
        method: "post",
        dataType: "json",
        beforeSend: function () {
            $('#loader_docPorDespachar').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { por_despachar } = data;
                $('#docPorDespachar').text(por_despachar);
            }
        },
        complete: function () {
            if(!isError) $('#loader_docPorDespachar').hide();
        }
    });
}

init();