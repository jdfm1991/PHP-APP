<?php

class EmailData
{
    public static function DataRecoverPassword($emailUser, $securityRandomNumber) {
        return array(
            'title'         => "Código de Seguridad para Recuperar Contraseña",
            'body'          => "El codigo de Seguridad es: <b>$securityRandomNumber</b> 
                                <p>Desarrollado Por equipo de IT. </b>Grupo Confisur IT -> The Innovation is our's priority..</p>",
            'recipients'    => array($emailUser), // puede ser mas de un destinatario
        );
    }
}