let estado_vacio;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    estado_vacio = true;
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
}

var no_puede_estar_vacio = function()
{
    ($("#fechai").val() !== "" && $("#fechaf").val() !== "" )
        ? estado_vacio = false : estado_vacio = true;
};

$(document).ready(function(){
    $("#fechai").change(() => no_puede_estar_vacio());
    $("#fechaf").change(() => no_puede_estar_vacio());
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {

    let fechai = $("#fechai").val();
    let fechaf = $("#fechaf").val();

    let tipo = $('input:radio[name=tipo]:checked').val();

    if (!estado_vacio && fechai !== "" && fechaf !== "") {
        window.open("recepcion_compras_tabla.php?&fechai="+fechai+"&fechaf="+fechaf+"&t="+tipo, '_blank');
        limpiar();
        estado_vacio = true;
    } else {

        if (fechai === "") {
            SweetAlertError('Debe Colocar la fecha inicial!');
            return false;
        }
        if (fechaf === "" ) {
            SweetAlertError('Debe Colocar la fecha final!');
            return false;
        }
        if (vendedor === "" ) {
            SweetAlertError('Debe seleccionar un vendedor!');
            return false;
        }
        if (marca === "" ) {
            SweetAlertError('Debe seleccionar una marca');
            return false;
        }
    }
});

init();
