<?php

class EmailData
{
    public static function DataRecoverPassword($emailUser, $securityRandomNumber) {
        return array(
            'title'      => "Código de Seguridad para Recuperar Contraseña",
            'body'       => "El codigo de Seguridad es: <b>$securityRandomNumber</b> 
                                <p>Desarrollado Por equipo de IT. </b>Grupo Confisur IT -> The Innovation is our's priority..</p>",
            'recipients' => array($emailUser), // puede ser mas de un destinatario
        );
    }

    public static function DataDespachoAgregarDocumento($data) {
        $usuario = $data['usuario'];
        $correl_despacho = str_pad($data['correl_despacho'], 8, 0, STR_PAD_LEFT);
        $correl_numero_pla = str_pad($data['nroplanilla'], 8, 0, STR_PAD_LEFT);
        $destino = $data['destino'];
        $chofer = $data['chofer'];
        $documento = $data['doc'];

        return array(
            'title'      => "SE HA AGREGADO UN DOCUMENTO AL DESPACHO NRO: $correl_despacho",
            'body'       => "$usuario, HA AGREGADO EL DOCUMENTO NRO: $documento, AL DESPACHO NRO: $correl_despacho, 
                            CON DESTINO AH: $destino, A CARGO DEL CHOFER: $chofer. 
                            <p> ESTE DESPACHO YA SE ENCONTRABA RELACIONADO, 
                            POR ENDE ES NECESARIO QUE SE REIMPRIMA LA PLANILLA DE RELACION DE CHOFERES NRO: $correl_numero_pla.</b>  
                            <p>Desarrollado Por equipo de IT. </b>Grupo Confisur IT -> The Innovation is our's priority..</p>",
            'recipients' => array(
                /*'dvilla@gconfisur.com',
                'rpenaloza@gconfisur.com',
                'jcaraballo@gconfisur.com',
                'cjimenez@gconfisur.com',
                'ctrujillo@gconfisur.com',*/
                'llopez@gconfisur.com'
            ),
        );
    }

    public static function DataDespachoEliminado($data) {
        $usuario = $data['usuario'];
        $correl_despacho = $data['correl_despacho'];
        $correl_chofer = $data['correl_chofer'];

        return array(
            'title'      => "SE HA ELIMINADO EL DESPACHO NRO $correl_despacho",
            'body'       => "$usuario, HA ELIMINADO EL DESPACHO NRO: $correl_despacho<p> 
                             POR ENDE TAMBIEN SE HA ELIMINADO LA PLANILLA DE RELACION DE CHOFERES NRO: $correl_chofer.</b> 
                             <p>Desarrollado Por equipo de IT. </b>Grupo Confisur IT -> The Innovation is our's priority..</p>",
            'recipients' => array(
                'dvilla@gconfisur.com',
                'rpenaloza@gconfisur.com',
                'jcaraballo@gconfisur.com',
                'cjimenez@gconfisur.com',
                'ctrujillo@gconfisur.com',
            ), // puede ser mas de un destinatario
        );
    }
}