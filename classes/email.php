<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email{
    
    public $nombre;
    public $email;
    public $token;


    public function __construct($nombre, $email, $token)
    {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->token = $token;
    }

    public function enviarConfirmacion(){
        //Crear objeto email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_HOST'];
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        $mail->setFrom('citas@luxmonastere.com', 'luxmonastere.com');
        $mail->addAddress('citas@luxmonastere.com');
        $mail->Subject = ('Confirma tu cuenta');
        
        //set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8'; 

        $contenido = "<html>";
        $contenido .= "<p> <strong>Hola" ." ". $this->nombre . "</strong> Has creado tu cuenta en Lux Monastere, sólo debes confirmarla</p>";       
        $contenido .= "<p>Presiona aquí: <a href='". $_ENV['APP_URL'] ."/confirmar-cuenta?token=". $this->token ."'>Confirmar Cuenta</a> </p>"; 
        $contenido .= "<p>Si tú no solicitaste esta cuenta, puedes ignorar el mensaje</p>"; 
        $contenido .= "</html>";

        $mail->Body = $contenido;

        //Enviar el email
        $mail->send();

        // debuguear($mail);
        
    }

    public function enviarInstrucciones(){
        //Crear objeto email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_HOST'];
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        $mail->setFrom('citas@luxmonastere.com', 'luxmonastere.com');
        $mail->addAddress('citas@luxmonastere.com');
        $mail->Subject = ('Reestablece tu password');
        
        //set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8'; 

        $contenido = "<html>";
        $contenido .= "<p> <strong>Hola". " " . $this->nombre . "</strong> Has solicitado reestablecer tu password, sigue el siguiente enlace</p>";       
        $contenido .= "<p>Presiona aquí: <a href='". $_ENV['APP_URL'] ."/recuperar?token=". $this->token ."'>Reestablecer password</a> </p>"; 
        $contenido .= "<p>Si tú no solicitaste reeestablecer el password de tu cuenta, puedes ignorar el mensaje</p>"; 
        $contenido .= "</html>";

        $mail->Body = $contenido;

        //Enviar el email
        $mail->send();
    }

}

?>