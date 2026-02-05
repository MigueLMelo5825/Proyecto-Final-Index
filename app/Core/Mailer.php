<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../libs/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/src/SMTP.php';

class Mailer {

  public static function enviar(string $toEmail, string $toName, string $subject, string $htmlBody, string $altBody): bool {

    $mail = new PHPMailer(true);

    try {
      // SMTP Gmail (como el PDF)
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = SMTP_USER;   // config
      $mail->Password = SMTP_PASS;   // contraseña de aplicación
      // Opción 1: SSL 465 (como la página 3)
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
      $mail->Port = 465;

      // (Alternativa: STARTTLS 587, el PDF lo menciona también)
      // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      // $mail->Port = 587;

      $mail->isHTML(true);
      $mail->CharSet = 'UTF-8';

      $mail->setFrom(SMTP_USER, 'IndexProyecto');
      $mail->addAddress($toEmail, $toName);

      $mail->Subject = $subject;
      $mail->Body    = $htmlBody;
      $mail->AltBody = $altBody;

      $mail->send();
      return true;

    } catch (Exception $e) {
      return false;
    }
  }
}


