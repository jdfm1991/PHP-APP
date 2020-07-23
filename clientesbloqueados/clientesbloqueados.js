var tabla_clientesbloqueados;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    $("#loader").hide();
    estado_minimizado = false;
    listar_vendedores();
}

function limpiar() {
    $("#vendedor").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla_clientesbloqueados.rows().count() === 0)
    ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

function listar_vendedores() {
    $.post("clientesbloqueados_controlador.php?op=listar_vendedores", function(data){
        data = JSON.parse(data);

        if(!jQuery.isEmptyObject(data.lista_vendedores)){
            //lista de seleccion de vendedores
            $('#vendedor').append('<option name="" value="">Seleccione</option>');
            $.each(data.lista_vendedores, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#vendedor').append('<option name="" value="' + opt.CodVend +'">' + opt.CodVend + ': ' + opt.Descrip + '</option>');
                // $('#vendedor').append('<option name="" value="' + opt.CodVend +'">' + opt.CodVend + ': ' + substr($query['Descrip'], 0, 35) + '</option>');
            });
        }
    });
}

$(document).ready(function(){
    $("#vendedor").change( () => estado_minimizado = true )
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_clientesbloqueados", function () {

    var vendedor = $("#vendedor").val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (vendedor !== "") {
            sessionStorage.setItem("vendedor", vendedor);
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla_clientesbloqueados = $('#clientesbloqueados_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    beforeSend: function () {
                        $("#loader").show(''); //MOSTRAMOS EL LOADER.
                    },
                    url: "clientesbloqueados_controlador.php?op=buscar_clientesbloqueados",
                    type: "post",
                    data: {vendedor: vendedor},
                    error: function (e) {
                        console.log(e.responseText);
                    },
                    complete: function () {

                        $("#tabla").show('');//MOSTRAMOS LA TABLA.
                        $("#loader").hide();//OCULTAMOS EL LOADER.
                        validarCantidadRegistrosTabla();
                        mostrar();
                        limpiar();//LIMPIAMOS EL SELECTOR.

                    }
                },//TRADUCCION DEL DATATABLE.
                "bDestroy": true,
                "responsive": true,
                "bInfo": true,
                "iDisplayLength": 10,
                "order": [[0, "desc"]],
                "language": texto_español_datatables
            });
            estado_minimizado = true;
        }
    } else {
        Swal.fire('Atención!', 'Debe seleccionar al menos un Vendedor!', 'error');
        return (false);
    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var vendedor = sessionStorage.getItem("vendedor");
    if (vendedor !== "") {
        window.location = "clientesbloqueados_excel.php?vendedor="+vendedor;
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var vendedor = sessionStorage.getItem("vendedor");
    if (vendedor !== "") {
        window.open('clientesbloqueados_pdf.php?&vendedor='+vendedor, '_blank');
    }
});

function mostrar() {

    var texto= 'Clientes Bloqueados: ';
    var cuenta =(tabla_clientesbloqueados.rows().count());
    $("#cuenta").html(texto + cuenta);
}

init();
