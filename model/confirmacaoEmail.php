<?php

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'lib/vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
class ConfirmacaoEmail{
    public function disparaEmail($dados){
        $mail = new PHPMailer(true);
        $chave = $dados['chave'];
        $usuario = $dados['usuario'];
        try{
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER; //só em desenvolvimento                      //Enable verbose debug output
            $mail->CharSet = "UTF-8";
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.mailtrap.io';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = '1940a9db445c74';                     //SMTP username
            $mail->Password   = '94e93e7a6c265e';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
            $mail->Port       = 2525;   

            //Recipients
            $mail->setFrom('no-reply@plix.com', 'Mailer');
            $mail->addAddress($usuario);     //Add a recipient
            //$mail->addAddress('ellen@example.com');               //Name is optional
            //$mail->addReplyTo('info@example.com', 'Information');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');
            
            //Content
            $mail->isHTML(true);   //Set email format to HTML
            $mail->Subject = 'PLIX Confirmação de e-mail';
            $mail->Body    = "Agradecemos o cadastro ao nosso site. <br><br>
            Falta pouco para você aproveitar os melhores filmes gratuitamente,
            basta clicar no link abaixo para confirmar seu email.<br><br> <a href='http://localhost:8080/login?chave=$chave'>Confirma Email</a><br>
            Você está recebendo este email do site PLIX. Ele serve puramente para confirmação da existência do email cadastrado na nossa plataforma.
            <br> ";
            $mail->AltBody = "Agradecemos o cadastro ao nosso site. \n\n
            Falta pouco para você aproveitar os melhores filmes gratuitamente,
            basta clicar no link abaixo para confirmar seu email.\n\n http://localhost:8080/login?chave=$chave \n\n
            Você está recebendo este email do site PLIX. Ele serve puramente para confirmação da existência do email cadastrado na nossa plataforma.
            \n ";

            $mail->send();
            return ['msg'=>"ok"];
            //$_SESSION["msg"] = "Usuário cadastrado com sucesso. Verifique seu e-mail.";
        
        }catch (Exception $e) {
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            //$_SESSION["msg"] = "Erro ao cadastrar usuário";
            //$_SESSION["msg"] = "{$mail->ErrorInfo}";
            return ['msg'=>$mail->ErrorInfo];
        }
    }
}