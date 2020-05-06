var tabla_por_despachar;

var tabla_despachos;

var estado_minimizado;

var registros_por_despachar;

var peso_max_vehiculo;

var peso_acum_facturas;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla_facturas_por_despachar").hide();
    $("#tabla_detalle_despacho").hide();
    $("#loader1").hide();
    $('.nextBtn').attr("disabled", true);
    $('.generar').attr("disabled", true);
    registros_por_despachar = "";
    peso_max_vehiculo = 0;
    peso_acum_facturas = 0;
    estado_minimizado = false;
}

function limpiar() {
    $("#fecha").val("");
    $("#chofer").val("");
    $("#vehiculo").val("");
    $("#destino").val("");
    $("#factura").val("");
    registros_por_despachar = "";
    peso_max_vehiculo = 0;
    peso_acum_facturas = 0;
}

function limpiar_campo_factura() {
    $("#factura").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla_despachos.rows().count() === 0)
        ? estado = true : estado = false;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function () {
    //VALIDA PARA HABILITAR EL BOTON SIGUIENTE
    estado = !($("#fecha").val().length > 0 && $("#chofer").val().length > 0 && $("#vehiculo").val().length > 0 && $("#destino").val().length > 0);
    $('.nextBtn').attr("disabled", estado); //boton siguiente
    
    //VALIDA PARA AÑADIR EN LA TABLA FACTURAS POR DESPACHAR DICHO REGISTRO
    estado1 = !($("#fecha").val().length > 0 && $("#chofer").val().length > 0 && $("#vehiculo").val().length > 0 && $("#destino").val().length > 0 && $("#factura").val().length > 0);
    $('.anadir').attr("disabled", estado1); //boton añadir
    // estado_minimizado = estado1;
};

$(document).ready(function () {
    var navListItems = $('div.setup-panel div a'), //botones steps
        allWells = $('.setup-content'), //step-2
        allNextBtn = $('.nextBtn'); //boton siguiente

    allWells.hide();

    navListItems.click(function (e) {
        e.preventDefault();
        var $target = $($(this).attr('href')),
            $item = $(this);

        if (!$item.hasClass('disabled')) {
            navListItems.removeClass('btn-primary').addClass('btn-default');
            $item.addClass('btn-primary');
            allWells.hide();
            $target.show();
            $target.find('input:eq(0)').focus();
        }
    });

    allNextBtn.click(function(){
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            curInputs = curStep.find("input[type='text'],input[type='url']"),
            isValid = true;

        $(".form-group").removeClass("has-error");
        for(var i=0; i<curInputs.length; i++){
            if (!curInputs[i].validity.valid){
                isValid = false;
                $(curInputs[i]).closest(".form-group").addClass("has-error");
            }
        }

        if (isValid)
            nextStepWizard.removeAttr('disabled').trigger('click');
    });

    $('div.setup-panel div a.btn-primary').trigger('click');

    //VALIDA CADA INPUT CUANDO ES CAMBIADO DE ESTADO
    $("#fecha").change(() => no_puede_estar_vacio());
    $("#chofer").change(() => no_puede_estar_vacio());
    $("#vehiculo").change(() => { no_puede_estar_vacio();
        cargarCapacidadVehiculo($("#vehiculo").val());
    });
    $("#destino").on('keyup', () => no_puede_estar_vacio()).keyup();
    $("#factura").on('keyup', () => no_puede_estar_vacio()).keyup();

});

function agregarCeros(fact){
    var cad_cero="";
    for(var i=0;i<(6-fact.length);i++)
        cad_cero+=0;
    return cad_cero+fact;
}

function gestionDeDocumentos(documento, operacion) {
    if(documento.length > 0) {
        switch (operacion) {
            case "agregar":
                registros_por_despachar += (documento + ";");
                break;
            case "eliminar":
                console.log(registros_por_despachar); // prueba
                console.log((documento + ";")); // prueba
                registros_por_despachar = registros_por_despachar.replace((documento + ";"), '');
                console.log(registros_por_despachar); // prueba
                cargarTabladeFacturasporDespachar();
                break;
        }
    }
}

function cargarCapacidadVehiculo(id) {
    $.ajax({
        url: "despachos_controlador.php?op=obtener_pesomaxvehiculo",
        method: "POST", data: {id: id},
        success: function (data) {
            data = JSON.parse(data);
            peso_max_vehiculo = data.capacidad;
        }
    });
}

function validarPesoporFactura(numero_fact){
    var resultado = false;
    if(numero_fact !== "") {
        $.ajax({
            async: false,
            url: "despachos_controlador.php?op=obtener_pesoporfactura",
            method: "POST",
            data: {numero_fact: numero_fact},
            success: function (data) {
                data = JSON.parse(data);
                if( (data.peso + peso_acum_facturas) > peso_max_vehiculo ){
                    Swal.fire('Atención!', 'El Vehiculo esta al maximo de Capacidad!', 'error');
                    resultado = false;
                } else {
                    resultado = true;
                }
            }
        });
        return resultado;
    }
}

function validarFacturaEnDespachos(numero_fact){
    var resultado = false;
    if(numero_fact !== "") {
        $.ajax({
            async: false,
            url: "despachos_controlador.php?op=buscar_facturaendespacho",
            method: "POST",
            data: {numero_fact: numero_fact},
            success: function (data) {
                data = JSON.parse(data);
                if( data.mensaje.toString().length > 0 ){
                    Swal.fire('Atención!', data.mensaje, 'error');
                    resultado = false;
                } else {
                    resultado = true;
                }
            }
        });
        return resultado;
    }
}

function validarExistenciaFactura(numero_fact){
    var resultado = false;
    if(numero_fact !== "") {
        $.ajax({
            async: false,
            url: "despachos_controlador.php?op=buscar_existefactura",
            method: "POST",
            data: {numero_fact: numero_fact, registros_por_despachar: registros_por_despachar},
            success: function (data) {
                data = JSON.parse(data);
                if( data.mensaje.toString().length > 0 ){
                    Swal.fire('Atención!', data.mensaje, 'error');
                    resultado = false;
                } else {
                    resultado = true;
                }
            }
        });
        return resultado;
    }
}

function cargarNuevoCorrelativo(){
    var correlativo = 0;
    $.ajax({
        async: false,
        url: "despachos_controlador.php?op=obtener_correlativo_despachos",
        method: "POST",
        success: function (data) {
            data = JSON.parse(data);
            correlativo = data.correlativo;
        }
    });
    return correlativo;
}

//ACCION AL PRECIONAR EL BOTON AÑADIR.
$(document).on("click", ".anadir", function () {

    var factura = agregarCeros($("#factura").val());

    validaciones = validarFacturaEnDespachos(factura) && validarPesoporFactura(factura) && validarExistenciaFactura(factura);

    if(validaciones) {
        //agregar factura por despachar
        gestionDeDocumentos(factura, "agregar");

        //cargar peso de la factura
        $.post("despachos_controlador.php?op=obtener_pesoporfactura", {numero_fact: factura}, function (data, status) {
            data = JSON.parse(data);
            peso_acum_facturas += data.peso;
        });

        //cargar tabla de facturas por despachar
        cargarTabladeFacturasporDespachar();
    }
});

//ACCION AL PRECIONAR EL BOTON GENERAR.
$(document).on("click", ".generar", function () {

    var fecha = $("#fecha").val();
    var chofer = $("#chofer").val();
    var vehiculo = $("#vehiculo").val();
    var destino = $("#destino").val().toUpperCase();
    usuario = $("#ci_usuario").val();

    if( peso_acum_facturas <= peso_max_vehiculo ){

        //OBTENER EL NUEVO NUMERO DE CORRELATIVO
        //ANTES DE INSERTAR EL DESPACHO
        correlativo = cargarNuevoCorrelativo();

        if (estado_minimizado) {
            $("#tabla_facturas_por_despachar").hide();
            //$("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
            // estado_minimizado = false;
            if (fecha !== "" && chofer !== "" && vehiculo !== "" && destino !== "" && registros_por_despachar.length > 0) {

                sessionStorage.setItem("fecha", fecha);
                sessionStorage.setItem("chofer", chofer);
                sessionStorage.setItem("vehiculo", vehiculo);
                sessionStorage.setItem("destino", destino);
                sessionStorage.setItem("documentos", registros_por_despachar);

                var mensaje = "";
                //INSERTAR EL NUEVO DESPACHO
                $.ajax({
                    async: false,
                    url: "despachos_controlador.php?op=registrar_despacho",
                    method: "POST",
                    data: {correlativo: correlativo, fechad: fecha, chofer: chofer, vehiculo: vehiculo, destino: destino, usuario: usuario, documentos: registros_por_despachar},
                    success: function (data) {
                        data = JSON.parse(data);
                        mensaje = data.mensaje;
                        // console.log(data);
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                        Toast.fire({
                            icon: data.icono,
                            title: data.mensaje
                        });
                    }
                });

                //verifica si el mensaje de insercion contiene error
                if(mensaje.includes('ERROR')) {
                    return (false);
                } else {
                    //en caso de no contener error, muestra la tabla
                    cargarTabladeProductosEnDespachoCreado(correlativo);
                }

                estado_minimizado = true;
            } else {
                Swal.fire('Atención!', 'NO TIENE FACTURAS SELECCIONADAS.', 'error');
                return (false);
            }
        }
    } else {
        Swal.fire('Atención!', 'EL PESO DE LA MERCANCIA SOBREPASA LA CAPACIDAD QUE SOPORTA ESTE CAMION.', 'error');
        return (false);

    }
});

function cargarTabladeFacturasporDespachar() {
    tabla_por_despachar = $('#fact_por_despachar_data').DataTable({
        "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
        "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
        "ajax": {
            beforeSend: function () {
                $("#loader1").show(''); //MOSTRAMOS EL LOADER.
            },
            url: "despachos_controlador.php?op=obtener_facturasporcargardespacho",
            type: "post",
            data: {registros_por_despachar: registros_por_despachar},
            error: function (e) {
                console.log(e.responseText);
            },
            complete: function () {
                //MUESTRA LA TABLA SI TIENE AL MENOS UN DESPACHO CARGADO EN MEMORIA POR REGISTRAR
                //Y HABILITA EL BOTON GENERAR DESPACHO
                if(registros_por_despachar.length > 0){
                    $('.generar').attr("disabled", false);//boton generar habilitado
                    $("#tabla_facturas_por_despachar").show();
                    estado_minimizado = true;
                } else {
                    $('.generar').attr("disabled", true);//boton generar inabilitado
                    $("#tabla_facturas_por_despachar").hide();
                    estado_minimizado = false;
                }

                $("#loader1").hide();//OCULTAMOS EL LOADER.
                // validarCantidadRegistrosTabla();
                limpiar_campo_factura();//LIMPIAMOS EL INPUT.
            }
        },//TRADUCCION DEL DATATABLE.
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]],
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
    });
}

function cargarTabladeProductosEnDespachoCreado(correlativo) {
    //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
    tabla_despachos = $('#despacho_general_data').DataTable({
        "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
        "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
        "ajax": {
            beforeSend: function () {
                $("#loader").show(''); //MOSTRAMOS EL LOADER.
            },
            url: "despachos_controlador.php?op=listar_despacho",
            type: "post",
            data: {correlativo: correlativo},
            error: function (e) {
                console.log(e.responseText);
            },
            complete: function () {

                $("#tabla").show('');//MOSTRAMOS LA TABLA.
                $("#loader").hide();//OCULTAMOS EL LOADER.
                validarCantidadRegistrosTabla();
                //mostrar();
                limpiar();//LIMPIAMOS EL SELECTOR.

            }
        },//TRADUCCION DEL DATATABLE.
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]],
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
    });
}

//ACCION AL PRECIONAR EL BOTON EXCEL.
/*$(document).on("click", "#btn_excel", function () {
    var opc = sessionStorage.getItem("opc", opc);
    var vendedor = sessionStorage.getItem("vendedor");
    if (opc !== "" && vendedor !== "") {
        window.location = "clientescodnestle_excel.php?&opc=" + opc + "&vendedor=" + vendedor;
    }
});*/

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click", "#btn_pdf", function () {
    var opc = sessionStorage.getItem("opc", opc);
    var vendedor = sessionStorage.getItem("vendedor");
    if (opc !== "" && vendedor !== "") {
        window.open('clientescodnestle_pdf.php?&opc=' + opc + '&vendedor=' + vendedor, '_blank');
    }
});





init();
