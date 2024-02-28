<?php

# URL  (principalmente para cargar librerias por url)
const URL_APP         = ("http://". SERVER ."/appweb-CONFIMANIA/app/");
const URL_LIBRARY     = ("http://". SERVER ."/appweb-CONFIMANIA/public/");
const URL_LANDINGPAGE = ("http://". SERVER ."/appweb-CONFIMANIA/public/landingpage/");
const URL_HELPERS_JS  = ("http://". SERVER ."/appweb-CONFIMANIA/helpers/js/");


# PATH  (los archivos php se cargan por ruta absoluta y no por url)
define("PATH_APP_PHP",     $_SERVER['DOCUMENT_ROOT'] ."/appweb-CONFIMANIA/app/");
define("PATH_HELPERS_PHP", $_SERVER['DOCUMENT_ROOT'] ."/appweb-CONFIMANIA/helpers/");
define("PATH_SERVICE_PHP", $_SERVER['DOCUMENT_ROOT'] ."/appweb-CONFIMANIA/services/");
define("PATH_LIBRARY",     $_SERVER['DOCUMENT_ROOT'] ."/appweb-CONFIMANIA/public/");
define("PATH_VENDOR",      $_SERVER['DOCUMENT_ROOT'] ."/appweb-CONFIMANIA/vendor/");
define("PATH_CONFIG",      $_SERVER['DOCUMENT_ROOT'] ."/appweb-CONFIMANIA/config/");


# Constantes de fecha
const FORMAT_DATE             = "d/m/Y";
const FORMAT_DATE_TO_EVALUATE = "Y-m-d";
const FORMAT_DATETIME         = "d/m/Y h:i:s";
const FORMAT_DATETIME2        = "d/m/Y h:i A";
const FORMAT_DATETIME_FOR_INSERT = "Y-m-d h:i:s";