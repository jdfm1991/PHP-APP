<?php


class Email {

    public static function valid_email($email)
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function send_email($title = 'empty', $body = '', $recipients=array())
    {
        require (PATH_VENDOR.'autoload.php');
        $mail = new PHPMailer\PHPMailer\PHPMailer();

        $mail->PluginDir = "";
        $mail->Mailer = "smtp";
        $mail->Host = EMAIL_HOST;
        $mail->Port= EMAIL_PORT;
        $mail->SMTPAuth = true;
        $mail->CharSet="utf-8";

        $mail->Username = EMAIL_USER;
        $mail->Password = EMAIL_PASSWORD;

        $mail->From = EMAIL_EMAILFROM;
        $mail->FromName = Empresa::getName();

        //El valor por defecto de Timeout es 10, le voy a dar un poco mas
        $mail->Timeout=10;

        if (is_array($recipients)==true and count($recipients)>0) {
            foreach ($recipients as $recipient) {
                $mail->AddAddress($recipient);
            }
            $mail->Subject = $title;
            $mail->Body = $body;
            $mail->AltBody = "Grupo Confisur IT -> The Innovation is our's priority..";

            return $mail->Send();
        }
        return false;
    }
}