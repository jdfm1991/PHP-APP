<?php

require_once("../../config/conexion.php");
/*if (!isset($_SESSION)) {

    header("Location:". URL_APP ."principal.php");

} else {*/

    if (isset($_POST["enviar"]) and $_POST["enviar"] == "si")
    {
        require_once("usuarios/Usuarios_modelo.php");
        $usuario = new Usuarios();
        $response = $usuario->login();

        if ($response['status']==='1')
        {
            include_once (PATH_HELPERS_PHP . "php/Session.php");
            Session::create($response['data']);

            header("Location:". URL_APP ."principal.php");
        }
    }

//}
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
  <!-- Bootstrap core CSS -->
  <link href="<?php echo URL_LIBRARY; ?>build/css/bootstrap.css" rel="stylesheet">
  <!-- Custom styles for this template -->
  <link href="<?php echo URL_LIBRARY; ?>build/css/signin.css" rel="stylesheet">
  <script language="javascript">
    function verificar(form) {
      if (form.login.value.length === " " || form.clave.value.length === " ") {
        alert('Llene los campos vacios');
        return false;
      }
    }
  </script>
</head>

<body class="text-center">
  <form class="form-signin" method="post">
    <img class="mb-4" src="<?php echo URL_LIBRARY; ?>build/images/logo.png" alt="" width="300" height="90">
    <h1 class="h3 mb-3 font-weight-normal">Inicio de Sesion</h1>
    <label for="login" class="sr-only">Dirección de Correo</label>
    <input id="login" name="login" class="form-control" placeholder="nombre de usuario" required="" autofocus="" type="text">
    <label for="clave" class="sr-only">Contraseña</label>
    <input id="clave" class="form-control" name="clave" placeholder="Contraseña" required="" type="password">
    <div class="form-group">
      <input type="hidden" name="enviar" class="form-control" value="si">
    </div>
    <button class="btn btn-lg btn-primary btn-block" type="submit" onClick="return verificar(this.form)">Acceder</button>
    <p class="mt-5 mb-3 text-muted">Desarrollaro por <a href="https://www.intecca.com.ve">INTEC C.A </a> <br />
      © <?php echo date("Y"); ?></p>
  </form>
</body>

</html>