<?php
/*

require (PATH_VENDOR.'autoload.php');
use Goutte\Client;

function is_connected()
{
    $connected = @fsockopen("es.stackoverflow.com", 80); 
    if ($connected){
        $is_conn = true; //Conectado
        fclose($connected);
    }else{
        $is_conn = false; //No conectado
    }
    
    return $is_conn;

}


$conectado = is_connected();

if ($conectado) {

?>
<div class="whatsapp_chat_support wcs_fixed_right" id="button-w">
    <div class="wcs_button_label">
       <strong>Tasa del Dia</strong>
    </div>  
    <div class="wcs_button wcs_button_circle">
        <span class="fa fa fa-usd"></span>
    </div>  
 
    <div class="wcs_popup">
        <div class="wcs_popup_close">
            <span class="fa fa-close"></span>
        </div>
        <div class="wcs_popup_header">
            <span class="fa fa-usd"></span>
            <?php
            
            $client = new Client();

            $url = "https://www.bcv.org.ve/";
            $crawler = $client->request('GET', $url);

            $dato = $crawler->filter("#dolar")->text();
            $tcr = $crawler->filter("#tcr")->text();
            $date = $crawler->filter(".dinpro")->text();

            $data = explode(" ", $dato);

            $valor = $data[1];

            $dolar = str_replace(",", ".", $valor);

            $tasa = number_format($dolar, 4, '.', '');

            ?>
            <strong><?php echo $tcr; ?></strong>
            
            <div class="wcs_popup_header_description">

            <label for="basic-url" class="form-label"><?php echo $data[0]; ?><span class="input-group-text" id="basic-addon1"><?php echo $tasa; ?></span></label>
            
            <span class="input-group-text" id="basic-addon1"><?php echo $date; ?></span>
            </div>

            <div class="wcs_popup_avatar">
                <img src="https://i0.wp.com/dhqr.me/wp-content/uploads/2014/06/logo-bcv.jpeg?ssl=1" alt="">
            </div>

        </div>  
    </div>
</div>

<?php } */?>



<!-- Main Footer -->
<footer class="main-footer">
	<strong>Copyright &copy; 2019-2020 <a href="http://www.intecca.com.ve">Innovación Tecnológica INTEC C.A</a>.</strong>
	Todos los derechos reservados.
	<div class="float-right d-none d-sm-inline-block">
		<b>Version</b> 1.0.0
	</div>
</footer>
<!-- ./wrapper -->
<script src="<?php echo URL_LIBRARY; ?>plugins/moment/moment.min.js"></script>
<!-- jQuery -->
<script src="<?php echo URL_LIBRARY; ?>plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo URL_LIBRARY; ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo URL_LIBRARY; ?>dist/js/adminlte.min.js"></script>
<!-- DataTables -->
<script src="<?php echo URL_LIBRARY; ?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo URL_LIBRARY; ?>plugins/datatables/jquery.columntoggle.js"></script>
<script src="<?php echo URL_LIBRARY; ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo URL_LIBRARY; ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<!--<script src="--><?php //echo URL_LIBRARY; ?><!--plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>-->
<script src="<?php echo URL_LIBRARY; ?>plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?php echo URL_LIBRARY; ?>plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?php echo URL_LIBRARY; ?>plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script src="<?php echo URL_LIBRARY; ?>plugins/bootbox/bootbox.min.js"></script>
<!-- sweetalert2 -->
<script src="<?php echo URL_LIBRARY; ?>plugins/sweetalert2/sweetalert2.all.min.js"></script>
<!-- date-range-picker -->
<script src="<?php echo URL_LIBRARY; ?>plugins/daterangepicker/daterangepicker.js"></script>
<!-- dashboard3 -->
<!--<script src="--><?php //echo URL_LIBRARY; ?><!--dist/js/pages/dashboard3.js"></script>-->
<script src="<?php echo URL_LIBRARY; ?>plugins/select2/js/select2.full.min.js"></script>
<!-- jquery-validation -->
<script src="<?php echo URL_LIBRARY; ?>plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="<?php echo URL_LIBRARY; ?>plugins/jquery-validation/additional-methods.min.js"></script>
<!-- InputMask -->
<script src="<?php echo URL_LIBRARY; ?>plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
<!-- OrgChart Plugin -->
<script src="<?php echo URL_LIBRARY;?>plugins/jquery-orgchart/js/jquery.orgchart.js"></script>
<!-- JQuery MoneyMask -->
<script src="<?php echo URL_LIBRARY; ?>plugins/jquery-maskmoney/jquery.maskMoney.min.js"></script>
<!-- Page script -->
<script src="<?php echo URL_HELPERS_JS; ?>SweetAlerts.js" type="text/javascript"></script>
<script src="<?php echo URL_HELPERS_JS; ?>Permissions.js" type="text/javascript"></script>
<script src="<?php echo URL_HELPERS_JS; ?>SendNotifications.js" type="text/javascript"></script>
<!-- Plugin JS file -->
<script src="<?php echo URL_LIBRARY; ?>plugin/components/moment/moment.min.js"></script>
<script src="<?php echo URL_LIBRARY; ?>plugin/components/moment/moment-timezone-with-data.min.js"></script> <!-- spanish language (es) -->
<script src="<?php echo URL_LIBRARY; ?>plugin/whatsapp-chat-support.js"></script><!--
<script>
   $('#button-w').whatsappChatSupport({
        defaultMsg : '',
    });
</script>-->
<script>
    const url = '<?php echo URL_APP; ?>';

    $.ajax({
        async: true,
        url: `${url}permiso/permiso_controlador.php?op=listar_permisos`,
        method: "POST",
        dataType: "json",
        data: {id: $("#id").val(), tipo: 1, esMenuLateral: 1},
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
        },
        success: function (data) {
            if (!jQuery.isEmptyObject(data)) {
                let menu = permisosMenuLateral(data);
                $('#content_menu ul li:last').before(menu);
            }
        },
    });

	$(function() {
		//Initialize Select2 Elements
		$('.select2').select2()

		//Initialize Select2 Elements
		$('.select2bs4').select2({
			theme: 'bootstrap4'
		})
        //Date range picker
        $('.daterangepicker').daterangepicker()
	})

    //variable global utilizada para traducir los textos de datatables a lenguaje español
    const texto_español_datatables = {
        "sProcessing": "Procesando...",
        "sLengthMenu": "Mostrar _MENU_ registros",
        "sZeroRecords": "No se encontraron resultados",
        "sEmptyTable": "Ningún dato disponible en esta tabla",
        "sInfo": "Mostrando un total de _TOTAL_ registros",
        "sInfoEmpty": "Mostrando un total de 0 registros",
        "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
        //"sInfoPostFix": "       Monto total de _TOTAL_ $",
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
    };

    // the loader html
    const sweet_loader = '<div class="sweet_loader"><svg viewBox="0 0 140 140" width="140" height="140"><g class="outline"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="rgba(0,0,0,0.1)" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"></path></g><g class="circle"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="#71BBFF" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-dashoffset="200" stroke-dasharray="300"></path></g></svg></div>';
</script>