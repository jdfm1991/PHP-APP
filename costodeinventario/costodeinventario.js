//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#loader").hide();

}

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_costodeinventario", function () {

    var marcas = document.getElementById('marca').value;

    if (marcas == "") {
        Swal.fire('Atención!','Seleccione una Marca!','error');
        return (false);
    }else {
        var datos=$('#frmCostos').serialize();
        $.ajax({
           beforeSend: function () {
            $("#loader").show();
        },
        type: "POST",
        url: "costodeinventario_controlador.php",
        data: datos,
        error: function(X){
            Swal.fire('Atención!','ha ocurrido un error!','error');
        },
        success: function(response) {
            $("#minimizar").slideToggle();
            $("#loader").hide();
            $("#costos_inv_ver").html(response);
        }
    });}
    });

/*//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
   var fechai = sessionStorage.getItem("fechai", fechai);
   var fechaf = sessionStorage.getItem("fechaf", fechaf);
   var marca = sessionStorage.getItem("marca", marca);
   if (fechai !== "" && fechaf !== "" && marca !== "") {
    window.location = "sellin_excel.php?&fechai="+fechai+"&fechaf="+fechaf+"&marca="+marca;
}
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    var marca = sessionStorage.getItem("marca", marca);
    if (fechai !== "" && fechaf !== "" && marca !== "") {
        window.open('sellin_pdf.php?&fechai='+fechai+'&fechaf='+fechaf+'&marca='+marca, '_blank');
    }
});
*/
/*function mostrar() {

    var texto= 'Clientes Sin Transacción:  ';
    var cuenta =(tabla_costodeinventario.rows().count());
    $("#cuenta").html(texto + cuenta);
}*/

init();
