<?php
require_once("../../config/conexion.php");
?>
<!DOCTYPE html>
<html lang="es-VE">

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Despacho y Logística">
    <meta name="author" content="INTEC C.A">
    <title>APP Despacho y Logística</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo URL_LIBRARY; ?>plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?php echo URL_LIBRARY; ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo URL_LIBRARY; ?>dist/css/adminlte.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <img class="mb-4" src="<?php echo URL_LIBRARY; ?>build/images/logo.png" alt="" width="300" height="90">
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Inicio de Sesión</p>

            <form id="login_form" method="post">
                <div class="input-group mb-3">
                    <input id="login" name="login" type="text" class="form-control" required="" autofocus="" autocomplete="off" placeholder="nombre de usuario">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input id="clave" name="clave" type="password" class="form-control" required="" placeholder="Contraseña">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-7">
                        <!--<div class="icheck-primary">
                            <input type="checkbox" id="remember">
                            <label for="remember">
                                Remember Me
                            </label>
                        </div>-->
                    </div>
                    <!-- /.col -->
                    <div class="col-5">
                        <button type="submit" class="btn btn-primary btn-block">Acceder</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
        </div>
        <!-- /.login-card-body -->
    </div>

    <div class="text-center">
        <p class="mt-1 mb-1 text-sm"><a href="#">Olvidé mi contraseña</a></p>

        <p class="mt-5 mb-3 text-muted">Desarrollaro por <a href="https://www.intecca.com.ve">INTEC C.A </a>  © <?php echo date("Y"); ?></p>
    </div>

</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="<?php echo URL_LIBRARY; ?>plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo URL_LIBRARY; ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo URL_LIBRARY; ?>dist/js/adminlte.min.js"></script>

</body>
</html>
