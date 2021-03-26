

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true : estado = false;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}


function listar_depositos_marcas(){

    // $.post("listadeprecio_controlador.php?op=listar_depositos_marcas", function(data){
    //     data = JSON.parse(data);
    //
    //     $('#depo').append('<option name="" value="">Seleccione Almacen</option>');
    //     $.each(data.lista_depositos, function(idx, opt) {
    //         //se itera con each para llenar el select en la vista
    //         $('#depo').append('<option name="" value="' + opt.codubi +'">'+ opt.codubi +': '+ opt.descrip.substr(0, 35) + '</option>');
    //     });
    //
    //     $('#marca').append('<option name="" value="">Seleccione una Marca</option>').append('<option name="" value="-">TODAS</option>');
    //     $.each(data.lista_marcas, function(idx, opt) {
    //         $('#marca').append('<option name="" value="' + opt.marca +'">' + opt.marca + '</option>');
    //     });
    // });


}

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    // var depos = sessionStorage.getItem("depos");
    // var marcas = sessionStorage.getItem("marcas");
    // var orden = sessionStorage.getItem("orden");
    // var p1 = sessionStorage.getItem("p1");
    // var p2 = sessionStorage.getItem("p2");
    // var p3 = sessionStorage.getItem("p3");
    // var iva = sessionStorage.getItem("iva");
    // var cubi = sessionStorage.getItem("cubi");
    // var exis = sessionStorage.getItem("exis");
    // window.location = "listadeprecio_excel.php?&depos="+depos+"&marcas="+marcas+""+"&orden="+orden+"&p1="+p1+"&p2="+p2+"&p3="+p3+"&iva="+iva+"&cubi="+cubi+"&exis="+exis;
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    // var depos = sessionStorage.getItem("depos");
    // var marcas = sessionStorage.getItem("marcas");
    // var orden = sessionStorage.getItem("orden");
    // var p1 = sessionStorage.getItem("p1");
    // var p2 = sessionStorage.getItem("p2");
    // var p3 = sessionStorage.getItem("p3");
    // var iva = sessionStorage.getItem("iva");
    // var cubi = sessionStorage.getItem("cubi");
    // var exis = sessionStorage.getItem("exis");
    // window.open("listadeprecio_pdf.php?&depos="+depos+"&marcas="+marcas+""+"&orden="+orden+"&p1="+p1+"&p2="+p2+"&p3="+p3+"&iva="+iva+"&cubi="+cubi+"&exis="+exis, '_blank');
});