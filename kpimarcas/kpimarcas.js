
//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#spinner").css('visibility', 'hidden');
    listar_marcas();

    //Bootstrap Duallistbox
    $('.duallistbox').bootstrapDualListbox({
        infoTextFiltered: '<span class="badge badge-warning">Filtrados</span> {0} de {1}',
        filterPlaceHolder: 'Filtro de Búsqueda',
        filterTextClear: 'show all',
        infoText: 'Total {0}',
        infoTextEmpty: 'Lista Vacía',
        moveSelectedLabel: 'Mover seleccionado',
        moveAllLabel: 'Mover todos',
        removeSelectedLabel: 'Remover seleccionado',
        removeAllLabel: 'Remover todos',
        selectorMinimalHeight: 240,
    })
}

function listar_marcas() {
    $.ajax({
        async: false,
        url: "kpimarcas_controlador.php?op=listar_marcas",
        type: "GET",
        error: function (e) {
            console.log(e.responseText);
        },
        success: function (data) {
            data = JSON.parse(data);

            if(!jQuery.isEmptyObject(data.lista_marcas)){
                $.each(data.lista_marcas, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('.duallistbox').append('<option name="" value="' + opt.marca +'" '+(opt.selec ? "selected" : "")+'>' + opt.marca + '</option>');
                });
            }
        }
    });
}

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_guardar", function () {

    let form = $('#frm_kpimarcas').serialize();

    $.ajax({
        async: true,
        url: "kpimarcas_controlador.php?op=guardar_kpiMarcas",
        method: "POST",
        data: form,
        beforeSend: function () {
            $("#spinner").css('visibility', 'visible'); //MOSTRAMOS EL LOADER.
        },
        error: function (e) {
            console.log(e.responseText);
            Swal.fire('Atención!','ha ocurrido un error!','error');
        },
        success: function (data) {
            data = JSON.parse(data);

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: false,
            })

            if(!jQuery.isEmptyObject(data))
                Toast.fire({icon: data.icono, title: data.mensaje})
             else
                Toast.fire({icon: 'error', title: 'Error al Guardar!'})
        },
        complete: function () {
            $("#spinner").css('visibility', 'hidden');
        }
    });
});

init();