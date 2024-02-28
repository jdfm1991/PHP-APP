
//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    listar_tabladinamica();
}

$(document).ready(function(){

});

function listar_tabladinamica(){

    let fechai   = $('#fechai').val();
    let fechaf   = $('#fechaf').val();
    let tipo     = $('#tipo').val();

    $.ajax({
        url: "recepcion_compras_controlador.php?op=listar_tabladinamica",
        method: "POST",
        data: {fechai: fechai, fechaf: fechaf, tipo: tipo},
        dataType: "json", // Formato de datos que se espera en la respuesta
        beforeSend: function () {
            SweetAlertLoadingShow("Procesando información, espere...");
        },
        error: function (e) {
            SweetAlertError(e.responseText.substring(0, 400) + "...", "Error!");
            console.log(e.responseText);
        },
        success: function (datos) {

            tabladinamica(datos);
            //tablaresumen(datos);

        },
        complete: function () {
            SweetAlertSuccessLoading();
            const arr = ['#tabla','#tabla1'];

            arr.forEach(val => {
                $(val).columntoggle({
                    //Class of column toggle contains toggle link
                    toggleContainerClass:'columntoggle-container',
                    //Text in column toggle box
                    toggleLabel:'MOSTRAR/OCULTAR CELDAS: ',
                    //the prefix of key in localstorage
                    keyPrefix:'columntoggle-',
                    //keyname in localstorage, if empty, it will get from URL
                    key:''

                });
            });
        }
    });
}

function tabladinamica(data) {
    let { tabla, totales } = data;

    if (!jQuery.isEmptyObject(tabla))
    {
        $.each(tabla, function(idx, opt) {
            $('#tabla').append(
                '<tr>' +
                '<td align="center" class="small align-middle">' + opt.num + '</td>' +
                '<td align="center" class="small align-middle">' + opt.CodProv + '</td>' +
                '<td align="center" class="small align-middle">' + opt.Descrip + '</td>' +
                '<td align="center" class="small align-middle">' + opt.NumeroD + '</td>' +
                '<td align="center" class="small align-middle">' + opt.CodItem + '</td>' +
                '<td align="center" class="small align-middle">' + opt.Descrip1 + '</td>' +
                '<td align="center" class="small align-middle">' + opt.Costo + '</td>' +
                '<td align="center" class="small align-middle">' + opt.Cantidad + '</td>' +
                '<td align="center" class="small align-middle">' + opt.TotalItem + '</td>' +
                '<td align="center" class="small align-middle">' + opt.fechae + '</td>' +
                '<td align="center" class="small align-middle">' + opt.tasa + '</td>' +
                '</tr>'
            );
        });
    }

    bold  = 'style="font-weight: bold"';
    $('#tabla').append('<tr><td colspan="20">'+ "" +'</td></tr>');
    $('#tabla').append(
        '<tr>' +
      
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'>Totales</td>' +
        '<td align="center" class="small text-right" ' + bold + '>' + totales.Costo + '</td>' +
        '<td align="center" class="small text-right" ' + bold + '>' + totales.Cantidad + '</td>' +
        '<td align="center" class="small text-right" ' + bold + '>' + totales.TotalItem + '</td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '</tr>'
    );/**/
}
/*
function tablaresumen(data) {
    let { resumen , total_descuento, total_descuentobs } = data;

    if (!jQuery.isEmptyObject(resumen))
    {
        $.each(resumen, function(idx, opt) {
            $('#tabla1').append(
                '<tr>' +
                '<td align="center" class="small align-middle">' + opt.codvend + '</td>' +
                '<td align="center" class="small align-middle">' + opt.codclie + '</td>' +
                '<td align="center" class="small align-middle">' + opt.descrip + '</td>' +
                '<td align="center" class="small text-right">' + opt.descuentototal + '</td>' +
                '<td align="center" class="small text-right">' + opt.tasa + '</td>' +
                '<td align="center" class="small text-right">' + opt.descuentototalbs + '</td>' +
                '<td align="center" class="small align-middle">' + opt.numerod + '</td>' +
                '<td align="center" class="small align-middle">' + opt.tipofac + '</td>' +
                '<td align="center" class="small align-middle">' + opt.fechae + '</td>' +
                '<td align="center" class="small align-middle">' + opt.mes + '</td>' +
                '</tr>'
            );

        });


        bold  = 'style="font-weight: bold"';
            $('#tabla1').append('<tr><td colspan="25">'+ "" +'</td></tr>');
            $('#tabla1').append(
                '<tr>' +
                '<td align="center" class="small text-right" '+bold+'></td>' +
                '<td align="center" class="small text-right" '+bold+'></td>' +
                '<td align="center" class="small text-right" '+bold+'>Totales</td>' +
                '<td align="center" class="small text-right" '+bold+'>' + total_descuento + '</td>' +
                '<td align="center" class="small text-right" '+bold+'></td>' +
                '<td align="center" class="small text-right" '+bold+'>' + total_descuentobs + '</td>' +
                '<td align="center" class="small text-right" '+bold+'></td>' +
                '<td align="center" class="small text-right" '+bold+'></td>' +
                '<td align="center" class="small text-right" '+bold+'></td>' +
                '<td align="center" class="small text-right" '+bold+'></td>' +
                '</tr>'
            );


    } else {
        //en caso de consulta vacia, mostramos un mensaje de vacio
        $('#tabla1').append('<tr><td colspan="7" align="center" class="small align-middle">Sin registros para esta Consulta</td></tr>');
    }
}*/

init();