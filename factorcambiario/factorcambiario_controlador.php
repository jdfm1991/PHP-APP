<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("factorcambiario_modelo.php");

require (PATH_VENDOR.'autoload.php');
use Goutte\Client;

//INSTANCIAMOS EL MODELO
$factor = new FactorCambiario();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "mostrar_factor":
        $datos = $factor->get_factor();

        $output["factor"] = (ArraysHelpers::validate($datos))
            ? Strings::rdecimal($datos[0]['factor'], 2)
            : 'error';

        echo json_encode($output);
        break;


    case "guardaryeditar":

        if (empty($_POST['factor_nuevo'])) {    
            
            function is_connected(){
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

                $client = new Client();
                $url = "https://www.bcv.org.ve/";
                $crawler = $client->request('GET', $url);

                $dato = $crawler->filter("#dolar")->text();
                $data = explode(" ", $dato);

                $valor = $data[1];

                $dolar = str_replace(",", ".", $valor);

                $factor_nuevo = number_format($dolar, 4, '.', '');

            }
        }else {
            $factor_nuevo = str_replace(",", ".", str_replace(".", "", $_POST['factor_nuevo']));
        }


        

        $guardar = $factor->editar_factor($factor_nuevo);

        //mensaje
        if ($guardar) {
            $output = [
                "mensaje" => "Guardado con Exito!",
                "icono"   => "success"
            ];
        } else {
            $output = [
                "mensaje" => "Ocurrió un error al Guardar!",
                "icono"   => "error",
                "valor" => $factor_nuevo
            ];
        }

        echo json_encode($output);
        break;
}
