<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../libs/ejemploPHPMailer/src/Exception.php';
require_once __DIR__ . '/../libs/ejemploPHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../libs/ejemploPHPMailer/src/SMTP.php';

class EmailService {
    
    private $mail;
    

    private const SMTP_HOST = 'smtp.gmail.com';
    private const SMTP_USER = 'puso87287@gmail.com';  //  EMAIL DE GMAIL
    private const SMTP_PASSWORD = 'pbebahyhktkjkpem';  // CONTRASEÑA DE APLICACIÓN DE GMAIL
    private const SMTP_PORT = 465;
    private const SMTP_FROM_EMAIL = 'puso87287@gmail.com';  //   EMAIL
    private const SMTP_FROM_NAME = 'Index';  //
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->configurarSMTP();
    }
    
    /**
     * Configura los parámetros SMTP
     */
    private function configurarSMTP() {
        try {
            // Configuración del servidor SMTP
            $this->mail->isSMTP();
            $this->mail->Host = self::SMTP_HOST;
            $this->mail->SMTPAuth = true;
            $this->mail->Username = self::SMTP_USER;
            $this->mail->Password = self::SMTP_PASSWORD;
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this->mail->Port = self::SMTP_PORT;
            
            // Configuración adicional
            $this->mail->CharSet = 'UTF-8';
            $this->mail->setLanguage('es', __DIR__ . '/PHPMailer/language/');
            
            // Para debugging (descomenta si hay problemas)
            // $this->mail->SMTPDebug = 2;
            
        } catch (Exception $e) {
            error_log("Error al configurar SMTP: " . $e->getMessage());
            throw new Exception("Error al configurar el servicio de email");
        }
    }
    
    /**
     * Envía un email de recuperación de contraseña
     * 
     * @param string $destinatario Email del destinatario
     * @param string $nombreDestinatario Nombre del destinatario
     * @param string $token Token de recuperación
     * @return bool True si se envió correctamente
     */
    public function enviarEmailRecuperacion($destinatario, $nombreDestinatario, $token) {
        try {
            // Remitente
            $this->mail->setFrom(self::SMTP_FROM_EMAIL, self::SMTP_FROM_NAME);
            
            // Destinatario
            $this->mail->addAddress($destinatario, $nombreDestinatario);
            
            // Configurar contenido
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Recuperación de contraseña - ' . self::SMTP_FROM_NAME;
            
            // Generar el enlace de recuperación
            // ⚠️ AJUSTA ESTA URL A TU PROYECTO
            $enlaceRecuperacion = $this->generarEnlaceRecuperacion($token);
            
            // Cargar plantilla HTML
            $cuerpoHTML = $this->cargarPlantillaRecuperacion($nombreDestinatario, $enlaceRecuperacion);
            
            $this->mail->Body = $cuerpoHTML;
            
            // Texto alternativo (sin HTML)
            $this->mail->AltBody = "Hola $nombreDestinatario,\n\n"
                . "Has solicitado restablecer tu contraseña.\n\n"
                . "Haz clic en el siguiente enlace para continuar:\n"
                . "$enlaceRecuperacion\n\n"
                . "Este enlace expirará en 1 hora.\n\n"
                . "Si no solicitaste este cambio, ignora este mensaje.\n\n"
                . "Saludos,\nEl equipo de " . self::SMTP_FROM_NAME;
            
            // Enviar
            $resultado = $this->mail->send();
            
            // Limpiar destinatarios para próximo envío
            $this->mail->clearAddresses();
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Error al enviar email de recuperación: " . $this->mail->ErrorInfo);
            return false;
        }
    }
    
    /**
     * Genera el enlace de recuperación
     * 
     * @param string $token Token de recuperación
     * @return string URL completa
     */
    private function generarEnlaceRecuperacion($token) {
        // AJUSTA ESTA URL SEGÚN TU CONFIGURACIÓN
        // Opción 1: Si usas index.php con parámetros
        $baseUrl = $this->obtenerBaseUrl();
        return $baseUrl . "?controller=recuperacion&action=restablecer&token=" . urlencode($token);
        
        // Opción 2: Si usas rutas más limpias, ajusta según corresponda
        // return $baseUrl . "/restablecer-password?token=" . urlencode($token);
    }
    
    /**
     * Obtiene la URL base del proyecto
     * 
     * @return string URL base
     */
    private function obtenerBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        
        // Obtener el directorio del proyecto
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $projectPath = dirname($scriptName);
        
        // Si estás en la raíz, dirname devuelve '/', sino devuelve el path
        $projectPath = ($projectPath === '/' || $projectPath === '\\') ? '' : $projectPath;
        
        return $protocol . "://" . $host . $projectPath;
    }
    
    /**
     * Carga la plantilla HTML del email de recuperación
     * 
     * @param string $nombre Nombre del destinatario
     * @param string $enlace Enlace de recuperación
     * @return string HTML del email
     */
    private function cargarPlantillaRecuperacion($nombre, $enlace) {
        // Ruta a la plantilla
        $rutaPlantilla = __DIR__ . '/../templates/email_recuperacion.html';
        
        // Si la plantilla existe, cargarla
        if (file_exists($rutaPlantilla)) {
            $plantilla = file_get_contents($rutaPlantilla);
            
            // Reemplazar variables
            $plantilla = str_replace('{{NOMBRE}}', htmlspecialchars($nombre), $plantilla);
            $plantilla = str_replace('{{ENLACE}}', htmlspecialchars($enlace), $plantilla);
            $plantilla = str_replace('{{APP_NAME}}', self::SMTP_FROM_NAME, $plantilla);
            
            return $plantilla;
        }
        
        // Si no existe la plantilla, usar una básica
        return $this->plantillaBasica($nombre, $enlace);
    }
    
    /**
     * Plantilla básica de email (fallback)
     * 
     * @param string $nombre Nombre del destinatario
     * @param string $enlace Enlace de recuperación
     * @return string HTML básico
     */
    private function plantillaBasica($nombre, $enlace) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background-color: #f4f4f4; padding: 20px; border-radius: 10px;'>
                <h2 style='color: #4a5568;'>Recuperación de contraseña</h2>
                <p>Hola <strong>" . htmlspecialchars($nombre) . "</strong>,</p>
                <p>Has solicitado restablecer tu contraseña en " . self::SMTP_FROM_NAME . ".</p>
                <p>Haz clic en el siguiente botón para continuar:</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='" . htmlspecialchars($enlace) . "' 
                       style='background-color: #4299e1; color: white; padding: 12px 30px; 
                              text-decoration: none; border-radius: 5px; display: inline-block;
                              font-weight: bold;'>
                        Restablecer contraseña
                    </a>
                </div>
                <p style='font-size: 14px; color: #666;'>
                    Este enlace expirará en 1 hora por seguridad.
                </p>
                <p style='font-size: 14px; color: #666;'>
                    Si no solicitaste este cambio, puedes ignorar este mensaje. 
                    Tu contraseña permanecerá sin cambios.
                </p>
                <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                <p style='font-size: 12px; color: #999;'>
                    Este es un mensaje automático, por favor no respondas a este correo.
                </p>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Envía un email genérico
     * 
     * @param string $destinatario Email del destinatario
     * @param string $asunto Asunto del email
     * @param string $cuerpo Cuerpo del email (HTML)
     * @param string $nombreDestinatario Nombre del destinatario (opcional)
     * @return bool True si se envió correctamente
     */
    public function enviarEmail($destinatario, $asunto, $cuerpo, $nombreDestinatario = '') {
        try {
            $this->mail->setFrom(self::SMTP_FROM_EMAIL, self::SMTP_FROM_NAME);
            $this->mail->addAddress($destinatario, $nombreDestinatario);
            $this->mail->isHTML(true);
            $this->mail->Subject = $asunto;
            $this->mail->Body = $cuerpo;
            
            $resultado = $this->mail->send();
            $this->mail->clearAddresses();
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Error al enviar email: " . $this->mail->ErrorInfo);
            return false;
        }
    }
}
