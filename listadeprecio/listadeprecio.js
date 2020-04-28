//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#loader").hide();

}

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_listadeprecio", function () {

    var depos = document.getElementById('depo').value;
    var marcas = document.getElementById('marca').value;
    var orden = document.getElementById('orden').value;
    var p1 = 0;
    var p2 = 0;
    var p3 = 0;
    var iva = 1;
    var cubi = 0;
    var exis = 0;

    if (depos == "") {
        Swal.fire('Atención!','Seleccione un Almacen!','error');
        return (false);
    }
    if (marcas == "") {
        Swal.fire('Atención!','Seleccione al menos una Marca!','error');
        return (false);
    }
    if (orden == "") {
        Swal.fire('Atención!',' Seleccione como desea Ordenar!','error');
        return (false);
    }
    if (document.getElementById('p1').checked) { p1 = 1; }
    if (document.getElementById('p2').checked) { p2 = 1; }
    if (document.getElementById('p3').checked) { p3 = 1; }
    if (document.getElementById('iva').checked) { iva = 1.16; }
    if (document.getElementById('cubi').checked) { cubi = 1; }
    if (document.getElementById('exis').checked) { exis = 1; }

    if (depos != "" && marcas != "" && orden != "") {
        $.ajax({
            type: "POST",
            url: "listadeprecio_controlador.php",
            data: {'depo': depos,'marca': marcas,'orden': orden,'p1': p1, 'p2': p2, 'p3': p3, 'iva': iva, 'cubi': cubi, 'exis':exis,} ,
            error: function(X){
                Swal.fire('Atención!','ha ocurrido un error!','error');
            },
            success: function(response) {
                $("#minimizar").slideToggle();
                $("#listadeprecio").html(response);
                $('#tablaprecios').dataTable({"responsive": true}).DataTable();

            }
        });
    }
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
