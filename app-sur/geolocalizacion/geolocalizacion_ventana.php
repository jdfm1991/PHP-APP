<?php
	//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");
session_start();
//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("geolocalizacion_modelo.php");

//INSTANCIAMOS EL MODELO
$geo = new Geolocalizacion();
	
	$latitud = 8.35122;
	$longitud = -62.64102;
	$cond1 = $cond2 = "";


	if ($_GET['vendedores'] != 'Todos') {
		$codvend = $_GET['vendedores'];
		$cond2 = " and codvend = '".$codvend."'";
	}
	if ($_GET['opc'] == 1) {
		$opc = 'lunes';
		$cond1 = " and DiasVisita = '".$opc."'";
	}elseif ($_GET['opc'] == 2) {
		$opc = 'martes';
		$cond1 = " and DiasVisita = '".$opc."'";
	}elseif ($_GET['opc'] == 3) {
		$opc = 'miercoles';
		$cond1 = " and DiasVisita = '".$opc."'";
	}elseif ($_GET['opc'] == 4) {
		$opc = 'jueves';
		$cond1 = " and DiasVisita = '".$opc."'";
	}elseif ($_GET['opc'] == 5) {
		$opc = 'viernes';
		$cond1 = " and DiasVisita = '".$opc."'";
	}

    $datos_clientes = $geo->getdata_cliente($_GET['opc'], $_GET['vendedores'],$cond1 ,$cond2);

	
	//if ($_GET['vendedores'] == 'Todos') {
?>
<html>
    <head>
	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
        <title>Geolocalización Distribuciones Confisur</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="../../public/leaflet/leaflet.css" />
		<script src="../../public/leaflet/leaflet.js"></script>
        <style>
        	#mapidt {
        		height: 100%;
        		width: 100%;
        	}
        </style>
	</head>
	<body>
        <h1>Geolocalización Distribuciones Confisur</h1>
        <div id="mapidt"></div>


<?php
	$array = array();
	$g = 0;

    echo "<script>console.log('count: " .  (count($datos_clientes)) . "' );</script>";

    foreach ($datos_clientes as $row) {

        if ( $row["latitud"]) {
			$latitud_ar = trim($row["latitud"]);
			$latitud_a = str_replace(',', '.', $latitud_ar);
			$longitud_ar = trim($row["longitud"] );
			$longitud_a = str_replace(',', '.', $longitud_ar);

			//$secuencia = trim(mssql_result($query, $i, 'secuencia'));
			if ((strlen($latitud_a) >= 5) and (strlen($longitud_a) >= 5)) {
				$descripc = trim(utf8_encode( $row["descrip"] ));
				$descrip = str_replace('á', 'a', $descripc);
				$descrip = str_replace('é', 'e', $descrip);
				$descrip = str_replace('í', 'i', $descrip);
				$descrip = str_replace('ó', 'o', $descrip);
				$descrip = str_replace('ú', 'u', $descrip);
				$array[$g] = array(
					'codvend' => trim( $row["codvend"] ),
					'descrip' => $descrip,
					'DiasVisita' => strtolower(trim( $row["DiasVisita"] )),
					'latitud' => $latitud_a,
					'longitud' => $longitud_a,
					//'secuencia' => $secuencia
				);
				$g++;
			}

		}
               
        
    }

    

	/*for ($i=0; $i < mssql_num_rows($query); $i++) {
		if ((mssql_result($query, $i, 'latitud'))) {
			$latitud_ar = trim(mssql_result($query, $i, 'latitud'));
			$latitud_a = str_replace(',', '.', $latitud_ar);
			$longitud_ar = trim(mssql_result($query, $i, 'longitud'));
			$longitud_a = str_replace(',', '.', $longitud_ar);
			$secuencia = trim(mssql_result($query, $i, 'secuencia'));
			if ((strlen($latitud_a) >= 5) and (strlen($longitud_a) >= 5)) {
				$descripc = trim(utf8_encode(mssql_result($query, $i, 'descrip')));
				$descrip = str_replace('á', 'a', $descripc);
				$descrip = str_replace('é', 'e', $descrip);
				$descrip = str_replace('í', 'i', $descrip);
				$descrip = str_replace('ó', 'o', $descrip);
				$descrip = str_replace('ú', 'u', $descrip);
				$array[$g] = array(
					'codvend' => trim(mssql_result($query, $i, 'codvend')),
					'descrip' => $descrip,
					'DiasVisita' => strtolower(trim(mssql_result($query, $i, 'DiasVisita'))),
					'latitud' => $latitud_a,
					'longitud' => $longitud_a,
					'secuencia' => $secuencia
				);
				$g++;
			}
		}else{
			echo "<script>console.log('Console: ERROR EN IF " . $i . "' );</script>";
		}
	}*/
?>
		<script>
			var latitud = "<?php echo $latitud; ?>";
			var longitud = "<?php echo $longitud; ?>";
			let map = L.map('mapidt').setView([latitud,longitud], 12);
			L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
			}).addTo(map);
			var R22 = L.icon({
				iconUrl: '../../public/leaflet/images/OT1.png',
			});
			var R99 = L.icon({
				iconUrl: '../../public/leaflet/images/OT2.png',
			});
			var BCLM01 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta1.png',
			});
			var CBLM01 = L.icon({
				iconUrl: '../../public/leaflet/images/VM.png',
			});
			var CBLM01A = L.icon({
				iconUrl: '../../public/leaflet/images/ruta2.png',
			});
			var CBLO01 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta3.png',
			});
			var CBLO01A = L.icon({
				iconUrl: '../../public/leaflet/images/ruta4.png',
			});
			var CCSD01 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta5.png',
			});
			var CCSM01 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta6.png',
			});
			var CCSO01 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta7.png',
			});
			var COMID01 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta8.png',
			});
			var MUNM01 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta9.png',
			});
			var MUNM01A = L.icon({
				iconUrl: '../../public/leaflet/images/ruta10.png',
			});



			var MUNO01 = L.icon({
				iconUrl: '../../public/leaflet/images/OT1.png',
			});
			var PGPM01 = L.icon({
				iconUrl: '../../public/leaflet/images/OT2.png',
			});
			var PZOD01 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta1.png',
			});
			var PZOD02 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta2.png',
			});
			var PZOD03 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta3.png',
			});
			var PZOD04 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta4.png',
			});
			var PZOD05 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta5.png',
			});
			var PZOM01 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta6.png',
			});
			var PZOM01A = L.icon({
				iconUrl: '../../public/leaflet/images/ruta7.png',
			});
			var PZOO01 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta8.png',
			});
			var PZOO01A = L.icon({
				iconUrl: '../../public/leaflet/images/ruta9.png',
			});
			var PZOSUP = L.icon({
				iconUrl: '../../public/leaflet/images/ruta10.png',
			});


			var SFD01 = L.icon({
				iconUrl: '../../public/leaflet/images/OT1.png',
			});
			var SFM01 = L.icon({
				iconUrl: '../../public/leaflet/images/OT2.png',
			});
			var SFM01A = L.icon({
				iconUrl: '../../public/leaflet/images/ruta1.png',
			});
			var SFM02 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta2.png',
			});
			var SFM02A = L.icon({
				iconUrl: '../../public/leaflet/images/ruta3.png',
			});
			var SFO01 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta4.png',
			});
			var SFO02 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta5.png',
			});
			var SFO03 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta6.png',
			});
			var SURM01 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta7.png',
			});
			var SURM01A = L.icon({
				iconUrl: '../../public/leaflet/images/ruta8.png',
			});
			var TIGM01 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta9.png',
			});
			var TIGM01A = L.icon({
				iconUrl: '../../public/leaflet/images/ruta10.png',
			});


			var TIGO01 = L.icon({
				iconUrl: '../../public/leaflet/images/OT1.png',
			});
			var UPD01 = L.icon({
				iconUrl: '../../public/leaflet/images/OT2.png',
			});
			var UPM01 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta1.png',
			});
			var UPO01 = L.icon({
				iconUrl: '../../public/leaflet/images/ruta2.png',
			});
			

			var marcadores = <?php echo json_encode($array) ?>;
			
			for (var i = 0; i < marcadores.length; i++) {
				if (marcadores[i].codvend=='22') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: R22}).addTo(map);
				}else if (marcadores[i].codvend=='99') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: R99}).addTo(map);
				}else if (marcadores[i].codvend=='BCLM01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: BCLM01}).addTo(map);
				}else if (marcadores[i].codvend=='CBLD08') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: CBLD08}).addTo(map);
				}else if (marcadores[i].codvend=='CBLM01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: CBLM01}).addTo(map);
				}else if (marcadores[i].codvend=='CBLM01A') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: CBLM01A}).addTo(map);
				}else if (marcadores[i].codvend=='CBLO01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: CBLO01}).addTo(map);
				}else if (marcadores[i].codvend=='CBLO01A') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: CBLO01A}).addTo(map);
				}else if (marcadores[i].codvend=='CCSD01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: CCSD01}).addTo(map);
				}else if (marcadores[i].codvend=='CCSM01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: CCSM01}).addTo(map);
				}else if (marcadores[i].codvend=='CCSO01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: CCSO01}).addTo(map);
				}else if (marcadores[i].codvend=='COMID01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: COMID01}).addTo(map);
				}else if (marcadores[i].codvend=='MUNM01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: MUNM01}).addTo(map);
				}else if (marcadores[i].codvend=='MUNM01A') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: MUNM01A}).addTo(map);
				}else if (marcadores[i].codvend=='MUNO01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: MUNO01}).addTo(map);
				}else if (marcadores[i].codvend=='PZOD01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: PZOD01}).addTo(map);
				}else if (marcadores[i].codvend=='PZOD02') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: PZOD02}).addTo(map);
				}else if (marcadores[i].codvend=='PZOD03') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: PZOD03}).addTo(map);
				}else if (marcadores[i].codvend=='PZOD04') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: PZOD04}).addTo(map);
				}else if (marcadores[i].codvend=='PZOD05') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: PZOD05}).addTo(map);
				}else if (marcadores[i].codvend=='PZOM01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: PZOM01}).addTo(map);
				}else if (marcadores[i].codvend=='PZOM01A') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: PZOM01A}).addTo(map);
				}else if (marcadores[i].codvend=='PZOO01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: PZOO01}).addTo(map);
				}else if (marcadores[i].codvend=='PZOO01A') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: PZOO01A}).addTo(map);
				}else if (marcadores[i].codvend=='PZOSUP') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: PZOSUP}).addTo(map);
				}else if (marcadores[i].codvend=='SFD01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: SFD01}).addTo(map);
				}else if (marcadores[i].codvend=='SFM01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: SFM01}).addTo(map);
				}else if (marcadores[i].codvend=='SFM01A') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: SFM01A}).addTo(map);
				}else if (marcadores[i].codvend=='SFM02') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: SFM02}).addTo(map);
				}else if (marcadores[i].codvend=='SFM02A') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: SFM02A}).addTo(map);
				}else if (marcadores[i].codvend=='SFO01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: SFO01}).addTo(map);
				}else if (marcadores[i].codvend=='SFO02') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: SFO02}).addTo(map);
				}else if (marcadores[i].codvend=='SFO03') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: SFO03}).addTo(map);
				}else if (marcadores[i].codvend=='SURM01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: SURM01}).addTo(map);
				}else if (marcadores[i].codvend=='SURM01A') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: SURM01A}).addTo(map);
				}else if (marcadores[i].codvend=='TIGM01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: TIGM01}).addTo(map);
				}else if (marcadores[i].codvend=='TIGM01A') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: TIGM01A}).addTo(map);
				}else if (marcadores[i].codvend=='TIGO01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: TIGO01}).addTo(map);
				}else if (marcadores[i].codvend=='UPD01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: UPD01}).addTo(map);
				}else if (marcadores[i].codvend=='UPM01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: UPM01}).addTo(map);
				}else if (marcadores[i].codvend=='UPO01') {
					var marcador = L.marker([marcadores[i].latitud,marcadores[i].longitud], {icon: UPO01}).addTo(map);
				}
                
				marcador.bindPopup(marcadores[i].descrip+'<br>Visita: '+marcadores[i].DiasVisita+'<br>ruta: '+marcadores[i].codvend);
			}
		</script>
    </body>
</html>
<?php
