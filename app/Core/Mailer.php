<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class Mailer {

  public static function enviar(string $toEmail, string $toName, string $subject, string $htmlBody, string $altBody): bool {

    $mail = new PHPMailer(true);

    try {
 
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'puso87287@gmail.com';   
      $mail->Password = 'pbebahyhktkjkpem';   // contraseña de aplicación
  
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
      $mail->Port = 465;

   
      $mail->isHTML(true);
      $mail->CharSet = 'UTF-8';

      $mail->setFrom('puso87287@gmail.com', 'IndexProyecto');
      $mail->addAddress($toEmail, $toName);

      $mail->Subject = $subject;
      $mail->Body    = $htmlBody;
      $mail->AltBody = $altBody;

      $mail->send();
      return true;

    } catch (Exception $e) {
      echo "<pre>";
      echo $mail->ErrorInfo;
      echo "</pre>";
      exit;
    }
  }
}


