let estado_vacio;


//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#loader").hide();
    $("#d_habiles").val(1);
    $("#d_transcurridos").val(0);
    estado_vacio = true;
}

function limpiar() {
    $("#fecha").val("01");
}

var no_puede_estar_vacio = function () {
    ($("#fecha").val() !== "" )
        ? estado_vacio = false : estado_vacio = true;
};



$(document).ready(function () {
    $("#fecha").change(() => no_puede_estar_vacio());
});


//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {

    let fecha = $("#fecha").val();

    if ( fecha !== "" ) {
        window.open("reporteKpiMarcas_tabla.php?&fecha=" + fecha, '_blank');
        limpiar();
        estado_vacio = true;
    } else {

        if (fecha === "") {
            Swal.fire('Atención!', 'Debe Colocar la fecha inicial!', 'error');
            return false;
        }
        
    }
});

init();
