var tabla_por_despachar;

var tabla_despachos;

var estado_minimizado;

// el formato sera:
//      numerod-tipofac-tara-cubicaje; numerod-tipofac-tara-cubicaje;
var registros_por_despachar;

var peso_max_vehiculo;

var cubicaje_max_vehiculo;

var cubicaje_acum_facturas;

var peso_acum_facturas;

var valor_bg_progreso;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#step1").trigger("click");
    VistasDeFormulario();
    $("#minimizar").slideDown();
    $("#tabla_facturas_por_despachar").hide();
    $("#tabla_detalle_despacho").hide();
    $( "#containerProgress" ).hide();
    $('.nextBtn').attr("disabled", true);
    $('.generar').attr("disabled", true);
    registros_por_despachar = "";
    peso_max_vehiculo = 0;
    cubicaje_max_vehiculo = 0;
    cubicaje_acum_facturas = 0;
    peso_acum_facturas = 0;
    estado_minimizado = false;
    valor_bg_progreso = "bg-success";
    listar_choferes();
    listar_vehiculo();
}

function limpiar() {
    $("#fecha").val("");
    $("#chofer").val("");
    $("#vehiculo").val("");
    $('#chofer').html("");
    $('#vehiculo').html("");
    $("#destino").val("");
    $("#numero_d").val("");
    registros_por_despachar = "";
    peso_max_vehiculo = 0;
    cubicaje_max_vehiculo = 0;
    cubicaje_acum_facturas = 0;
    peso_acum_facturas = 0;
    valor_bg_progreso = "bg-success";
}

function limpiar_campo_factura() {
    $("#numero_d").val("");
}

function limpiar_campo_factura_modal() {
    $("#nrodocumento").val("");
    $("#detalle_despacho").html("");
}

/*************************************************************************************************************/
/*                                             VALIDACIONES                                                  */
/*************************************************************************************************************/

function validarCantidadRegistrosTabla() {
    (tabla_despachos.rows().count() === 0)
        ? estado = true : estado = false;
    $('#btn_pdf').attr("disabled", estado);
}

let no_puede_estar_vacio = function () {
    //VALIDA PARA HABILITAR EL BOTON SIGUIENTE
    estado = !($("#fecha").val().length > 0 && $("#chofer").val().length > 0 && $("#vehiculo").val().length > 0 && $("#destino").val().length > 0);
    $('.nextBtn').attr("disabled", estado); //boton siguiente
    
    //VALIDA PARA AÑADIR EN LA TABLA FACTURAS POR DESPACHAR DICHO REGISTRO
    estado1 = !($("#fecha").val().length > 0 && $("#chofer").val().length > 0 && $("#vehiculo").val().length > 0 && $("#destino").val().length > 0 && $("#numero_d").val().length > 0);
    $('.anadir').attr("disabled", estado1); //boton añadir
    // estado_minimizado = estado1;
};

function onPressKey(e) {
    e.preventDefault(); //No se activará la acción predeterminada del evento
    tecla = (document.all) ? e.keyCode : e.which;
    if (tecla===13) anadirDocumentoPorDespachar();
}

/*************************************************************************************************************/
/*                                         VALIDACIONES CON AJAX                                             */
/*************************************************************************************************************/

function cargarCapacidadVehiculo(id) {
    $.ajax({
        url: "despachos_controlador.php?op=obtener_pesomaxvehiculo",
        method: "POST",
        dataType: "json",
        data: {id: id},
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            peso_max_vehiculo = data.capacidad;
            cubicaje_max_vehiculo = data.cubicajeMax;
        }
    });
}

function buscarFacturaEnDespachos(nrofact){
    if (nrofact !== "") {
        nrofact = addZeros(nrofact);
        $("#detalle_despacho").html("");
        $.ajax({
            url: "despachos_controlador.php?op=buscar_facturaEnDespachos_modal",
            type: "POST",
            dataType: "json",
            data: {nrfactb: nrofact},
            error: function (e) {
                SweetAlertError(e.responseText, "Error!")
                send_notification_error(e.responseText);
                console.log(e.responseText);
            },
            success: function (data) {
                if(!jQuery.isEmptyObject(data.factura_en_despacho)){
                    $("#detalle_despacho").append(
                        '<p>' +
                        '<strong>Nro de Documento: </strong>  ' + nrofact + '  ' +
                        '<strong>Despacho Nro: </strong>  ' + data.factura_en_despacho.Correlativo + '<br>' +
                        '<strong>Fecha Emision: </strong>  ' + data.factura_en_despacho.fechae + '  ' +
                        '<strong>Despacho Nro: </strong>  ' + data.factura_en_despacho.Destino +
                        '</p>'
                    );

                    if(!jQuery.isEmptyObject(data.datos_pago)){
                        $("#detalle_despacho").append(
                            '<p>' +
                            '<strong>PAGO: </strong>  ' + data.datos_pago.fecha_liqui +  '  ' +
                            '<strong>POR UN MONTO DE: </strong>  ' + data.datos_pago.monto_cancelado + ' BsS' +
                            '</p>'
                        );
                    }else{
                        $("#detalle_despacho").append('<br>DOCUMENTO NO LIQUIDADO');
                    }
                } else {
                    $("#detalle_despacho").append('<br>EL DOCUMENTO INGRESADO <strong>NO A SIDO DESPACHADO</strong>');
                }
            }
        });
    } else {
        $("#detalle_despacho").html("");
    }
}

function listar_choferes(){
    $.ajax({
        url: "despachos_controlador.php?op=listar_choferes",
        type: "POST",
        dataType: "json",
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            //lista de seleccion de choferes
            $('#chofer').append('<option name="" value="">Seleccione</option>');
            $.each(data.lista_choferes, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#chofer').append('<option name="" value="' + opt.Cedula +'">' + opt.Nomper + '</option>');
            });
        }
    });
}

function listar_vehiculo(){
    $.ajax({
        url: "despachos_controlador.php?op=listar_vehiculo",
        type: "POST",
        dataType: "json",
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            //lista de seleccion de vehiculos
            $('#vehiculo').append('<option name="" value="">Seleccione</option>');
            $.each(data.lista_vehiculos, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#vehiculo').append('<option name="" value="' + opt.id +'">' + opt.modelo + "  " + opt.capacidad + " Kg" + '</option>');
            });
        }
    });
}

/*************************************************************************************************************/
/*                                                  EVENTOS                                                  */
/*************************************************************************************************************/

$(document).ready(function () {
    VistasDeFormulario();

    //VALIDA CADA INPUT CUANDO ES CAMBIADO DE ESTADO
    $("#fecha").change(() => no_puede_estar_vacio());
    $("#chofer").change(() => no_puede_estar_vacio());
    $("#vehiculo").change(() => { no_puede_estar_vacio();
        cargarCapacidadVehiculo($("#vehiculo").val());
    });
    $("#destino").on('keyup', () => no_puede_estar_vacio()).keyup();
    $("#numero_d").on('keyup', () => no_puede_estar_vacio()).keyup();

    /*$('#numero_d').keypress(function(e){
        onPressKey(e)
    });*/
});

function VistasDeFormulario() {

    /*
    ESTA FUNCION AUTOGESTIONA LA VISTA DE CREAR EL DESPACHO DE ACUERDO A LOS
    DATOS INGRESADOS EN LA PRIMERA VISTA, EL BOTON SIGUIENTE LLEVA A LO QUE ES
    EL INGRESO DE FACTURAS A UN DESPACHOS PARA POSTERIORMENTE GENERAR EL DESPACHO
     */

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
}

/*************************************************************************************************************/
/*                                          GESTION DE DOCUMENTOS                                            */
/*************************************************************************************************************/

function anadir(documento, tipofac, tara, cubicaje) {
    if(documento.length > 0) {
        registros_por_despachar += (`${documento}-${tipofac}-${tara}-${cubicaje};`);
    }
}

function eliminar(documento) {
    if(documento.length > 0) {
        $.ajax({
            url: "despachos_controlador.php?op=obtener_pesoporfactura",
            type: "POST",
            dataType: "json",
            data: {
                numero_fact: documento,
                peso_acum_facturas: peso_acum_facturas,
                peso_max_vehiculo: peso_max_vehiculo,
                cubicaje_acum_facturas: cubicaje_acum_facturas,
                cubicaje_max_vehiculo: cubicaje_max_vehiculo,
                eliminarPeso: "si"
            },
            error: function (e) {
                SweetAlertError(e.responseText, "Error!")
                send_notification_error(e.responseText);
                console.log(e.responseText);
            },
            success: function (data) {
                //asignamos el peso acumulado restandole la factura a eliminar
                peso_acum_facturas = data.pesoNuevoAcum.toString();
                cubicaje_acum_facturas = data.cubicajeNuevoAcum.toString();

                //eliminamos la factura del string
                registros_por_despachar = registros_por_despachar.replace((documento + ";"), '');

                //seteamos la barra de progreso
                barraDeProgreso(data.bgProgreso, data.pesoNuevoAcum, data.porcentajePeso, data.cubicajeNuevoAcum, data.porcentajeCubicaje);

                //recargar la tabla
                cargarTabladeFacturasporDespachar();
            }
        });
    }
}

function barraDeProgreso(colorFondo, pesoAcumulado, porcentajePeso, cubicajeAcumlulado, porcentajeCubicaje){
    //modifica el texto de los kilos acumulados vs el maximo de carga
    $( "#textoBarraProgreso" )
        .removeClass(valor_bg_progreso)
        .addClass(colorFondo)
        .text(pesoAcumulado+" kg  /  "+peso_max_vehiculo+" kg"+"   ("+parseInt(porcentajePeso)+"%)");

    //modifica la bara de progreso del peso acumulado
    $("#barraProgreso")
        .removeClass(valor_bg_progreso)
        .addClass(colorFondo)
        .css('width', porcentajePeso+'%')
        .attr("aria-valuenow", porcentajePeso);

    //modifica el texto del cubicaje acumulado vs el maximo de volumen
    $( "#textoBarraProgresoCubicaje" )
        .text(cubicajeAcumlulado+" cm3  /  "+cubicaje_max_vehiculo+" cm3"+"   ("+parseInt(porcentajeCubicaje)+"%)");

    //modifica la bara de progreso del cubicaje acumulado
    $("#barraProgresoCubicaje")
        .css('width', porcentajeCubicaje+'%')
        .attr("aria-valuenow", porcentajeCubicaje);

    //guardamos el valor del background del peso acumulado
    valor_bg_progreso = colorFondo;
}

/*************************************************************************************************************/
/*                                          EVENTOS DE CLICK A BOTONES                                       */
/*************************************************************************************************************/

//ACCION AL PRECIONAR EL BOTON AÑADIR.
$(document).on("click", ".anadir", function () {
    anadirDocumentoPorDespachar();
});

function anadirDocumentoPorDespachar() {

    const tipodoc = $('input:radio[name=tipo]:checked').val()
    const documento = addZeros($("#numero_d").val());

    $.ajax({
        url: "despachos_controlador.php?op=validar_documento_para_anadir",
        method: "post",
        dataType: "json",
        data: {
            documento: documento, tipodoc: tipodoc,
            registros_por_despachar: registros_por_despachar,
            peso_acum_facturas: peso_acum_facturas,
            peso_max_vehiculo: peso_max_vehiculo,
            cubicaje_acum_facturas: cubicaje_acum_facturas,
            cubicaje_max_vehiculo: cubicaje_max_vehiculo
        },
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if( data.cond === 'false' ){
                SweetAlertError(data.mensaje);
            } else {

                // agregar documento por despachar
                anadir(data.numerod, data.tipodoc, data.peso, data.cubicaje);

                peso_acum_facturas = data.pesoNuevoAcum.toString();
                cubicaje_acum_facturas = data.cubicajeNuevoAcum.toString();

                // seteamos la barra de progreso
                barraDeProgreso(data.bgProgreso, data.pesoNuevoAcum, data.porcentajePeso, data.cubicajeNuevoAcum, data.porcentajeCubicaje);
            }

        }
    });



    //cargar peso de la factura
    $.ajax({
        url: "despachos_controlador.php?op=obtener_pesoporfactura",
        type: "POST",
        dataType: "json",
        data: {numero_fact: factura, peso_acum_facturas: peso_acum_facturas, peso_max_vehiculo:peso_max_vehiculo, cubicaje_acum_facturas: cubicaje_acum_facturas, cubicaje_max_vehiculo: cubicaje_max_vehiculo},
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            peso_acum_facturas = data.pesoNuevoAcum.toString();
            cubicaje_acum_facturas = data.cubicajeNuevoAcum.toString();

            //seteamos la barra de progreso
            barraDeProgreso(data.bgProgreso, data.pesoNuevoAcum, data.porcentajePeso, data.cubicajeNuevoAcum, data.porcentajeCubicaje);
        }
    });

    //cargar tabla de facturas por despachar
    cargarTabladeFacturasporDespachar();

    //inabilita el boton añadir
    $('.anadir').attr("disabled", true);

}

//ACCION AL PRECIONAR EL BOTON GENERAR.
$(document).on("click", ".generar", function () {

    var fecha = $("#fecha").val();
    var chofer = $("#chofer").val();
    var vehiculo = $("#vehiculo").val();
    var destino = $("#destino").val().toUpperCase();
    var usuario = $("#ci_usuario").val();

    if( parseFloat(peso_acum_facturas) <= parseFloat(peso_max_vehiculo) ){

        if (estado_minimizado) {
            $("#tabla_facturas_por_despachar").hide();
            $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
            // estado_minimizado = false;
            if (fecha !== "" && chofer !== "" && vehiculo !== "" && destino !== "" && registros_por_despachar.length > 0) {

                //INSERTAR EL NUEVO DESPACHO
                $.ajax({
                    url: "despachos_controlador.php?op=registrar_despacho",
                    method: "POST",
                    dataType: "json",
                    data: {fechad: fecha, chofer: chofer, vehiculo: vehiculo, destino: destino, usuario: usuario, documentos: registros_por_despachar},
                    error: function (e) {
                        SweetAlertError(e.responseText, "Error!")
                        send_notification_error(e.responseText);
                        console.log(e.responseText);
                    },
                    success: function (data) {
                        let {icono, mensaje} = data;
                        ToastSweetMenssage(icono, mensaje);

                        //verifica si el mensaje de insercion contiene error
                        if(mensaje.includes('ERROR')) {
                            return (false);
                        } else {
                            sessionStorage.setItem("correl", data.correl);
                            //en caso de no contener error, muestra la tabla
                            cargarTabladeProductosEnDespachoCreado(data.correl);
                        }
                    }
                });

                estado_minimizado = true;
            } else {
                SweetAlertError('NO TIENE FACTURAS SELECCIONADAS.', "Atención!")
                return (false);
            }
        }
    } else {
        SweetAlertError('EL PESO DE LA MERCANCIA SOBREPASA LA CAPACIDAD QUE SOPORTA ESTE CAMION.', "Atención!")
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click", "#btn_newdespacho", function () {
    limpiar();
    init();
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click", "#btn_pdf", function () {
    const correl = sessionStorage.getItem("correl");
    if (correl !== "") {
        window.open('despachos_pdf.php?&correlativo=' + correl, '_blank');
    }
});

//ACCION AL PRECIONAR EL BOTON BUSCAR.
$(document).on("click", "#btnBuscarFactModal", function () {
    const fact = $("#nrodocumento").val();
    buscarFacturaEnDespachos(fact);
});

/*************************************************************************************************************/
/*                                                TABLAS                                                     */
/*************************************************************************************************************/


function cargarTabladeFacturasporDespachar() {
    let isError = false;
    if(registros_por_despachar.toString().length > 0){
        tabla_por_despachar = $('#fact_por_despachar_data').dataTable({
            "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
            "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
            "ajax": {
                url: "despachos_controlador.php?op=obtener_facturasporcargardespacho",
                type: "post",
                dataType: "json",
                data: {registros_por_despachar: registros_por_despachar},
                beforeSend: function () {
                    SweetAlertLoadingShow();
                },
                error: function (e) {
                    isError = SweetAlertError(e.responseText, "Error!")
                    send_notification_error(e.responseText);
                    console.log(e.responseText);
                },
                complete: function () {
                    //MUESTRA LA TABLA SI TIENE AL MENOS UN DESPACHO CARGADO EN MEMORIA POR REGISTRAR
                    //Y HABILITA EL BOTON GENERAR DESPACHO
                    if(registros_por_despachar.length > 0){
                        $('.generar').attr("disabled", false);//boton generar habilitado
                        $("#tabla_facturas_por_despachar").show();
                        $( "#containerProgress" ).show();
                        estado_minimizado = true;
                    } else {
                        $('.generar').attr("disabled", true);//boton generar inabilitado
                        $("#tabla_facturas_por_despachar").hide();
                        $( "#containerProgress" ).hide();
                        estado_minimizado = false;
                    }

                    if(!isError) SweetAlertLoadingClose();
                    limpiar_campo_factura();//LIMPIAMOS EL INPUT.
                }
            },//TRADUCCION DEL DATATABLE.
            "bDestroy": true,
            "responsive": true,
            "bInfo": true,
            "iDisplayLength": 10,
            "order": [[0, "desc"]],
            "language": texto_español_datatables
        });
    } else {
        $("#tabla_facturas_por_despachar").hide();
        $( "#containerProgress" ).hide();
    }
}

function cargarTabladeProductosEnDespachoCreado(correlativo) {

    let isError = false;
    //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
    $.ajax({
        url: "despachos_controlador.php?op=listar_despacho",
        type: "POST",
        data: {correlativo: correlativo},
        dataType: "json",
        beforeSend: function () {
            SweetAlertLoadingShow();
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function(data) {
            if(!jQuery.isEmptyObject(data)) {
                let {contenido_tabla, totales_tabla} = data;
                //TABLA
                tabla_despachos = $('#despacho_general_data').dataTable({
                    "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                    "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.

                    "sEcho": contenido_tabla.sEcho, //INFORMACION PARA EL DATATABLE
                    "iTotalRecords": contenido_tabla.iTotalRecords, //TOTAL DE REGISTROS AL DATATABLE.
                    "iTotalDisplayRecords": contenido_tabla.iTotalDisplayRecords, //TOTAL DE REGISTROS A VISUALIZAR.
                    "aaData": contenido_tabla.aaData, // informacion por registro


                    "bDestroy": true,
                    "responsive": true,
                    "bInfo": true,
                    "iDisplayLength": 10,
                    "order": [[0, "desc"]],
                    "language": texto_español_datatables
                });

                const texto = "Total Bultos: " + totales_tabla.total_bultos + "  Total Pag: " + totales_tabla.total_paq;
                $("#cuenta").html(texto);

                $("#cantBul_tfoot").text(totales_tabla.total_bultos);
                $("#cantPaq_tfoot").text(totales_tabla.total_paq);
            }
        },
        complete: function () {
            $("#tabla_detalle_despacho").show('');//MOSTRAMOS LA TABLA.
            if(!isError) SweetAlertLoadingClose();
            validarCantidadRegistrosTabla();
        }
    });
}


/*************************************************************************************************************/
/*                                            INICIALIZAR                                                    */
/*************************************************************************************************************/


init();
