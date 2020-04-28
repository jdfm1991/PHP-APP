<!-- Main Footer -->
<footer class="main-footer">
	<strong>Copyright &copy; 2019-2020 <a href="http://www.intecca.com.ve">Innovación Tecnológica INTEC C.A</a>.</strong>
	Todos los derechos reservados.
	<div class="float-right d-none d-sm-inline-block">
		<b>Version</b> 1.0.0
	</div>
</footer>
</div>
<!-- ./wrapper -->
<!-- jQuery -->
<script src="<?php echo SERVERURL; ?>public/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo SERVERURL; ?>public/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo SERVERURL; ?>public/dist/js/adminlte.min.js"></script>
<script src="<?php echo SERVERURL; ?>public/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo SERVERURL; ?>public/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo SERVERURL; ?>public/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo SERVERURL; ?>public/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?php echo SERVERURL; ?>public/plugins/bootbox/bootbox.min.js"></script>
<!-- sweetalert2 -->
<script src="<?php echo SERVERURL; ?>public/plugins/sweetalert2/sweetalert2.all.min.js"></script>
<!-- dashboard3 -->
<script src="<?php echo SERVERURL; ?>public/plugins/chart.js/Chart.min.js"></script>
<script src="<?php echo SERVERURL; ?>public/dist/js/pages/dashboard3.js"></script>
<script src="<?php echo SERVERURL; ?>public/plugins/select2/js/select2.full.min.js"></script>
<!-- Page script -->
<script>
	$(function () {
//Initialize Select2 Elements
$('.select2').select2()

//Initialize Select2 Elements
$('.select2bs4').select2({
	theme: 'bootstrap4'
})
})
</script>
</body>
</html>
