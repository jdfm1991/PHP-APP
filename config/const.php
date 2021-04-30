<?php

#ROUTE
const SERVER = ("localhost");


# URL  (principalmente para cargar librerias por url)
const URL_APP         = ("http://". SERVER ."/appweb/app/");
const URL_LIBRARY     = ("http://". SERVER ."/appweb/public/");
const URL_LANDINGPAGE = ("http://". SERVER ."/appweb/public/landingpage/");
const URL_HELPERS_JS  = ("http://". SERVER ."/appweb/helpers/js/");


# PATH  (los archivos php se cargan por ruta absoluta y no por url)
define("PATH_HELPERS_PHP", $_SERVER['DOCUMENT_ROOT'] ."/appweb/helpers/");
define("PATH_LIBRARY",     $_SERVER['DOCUMENT_ROOT'] ."/appweb/public/");
