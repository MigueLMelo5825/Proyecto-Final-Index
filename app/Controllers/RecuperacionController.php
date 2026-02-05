<?php


require_once __DIR__ . '/../Models/UsuarioModel.php';
require_once __DIR__ . '/../Models/TokenRecuperacion.php';
require_once __DIR__ . '/../libs/EmailService.php';

class RecuperacionController {
    
    private $usuarioModel;
    private $tokenModel;
    private $emailService;
    
    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
        $this->tokenModel = new TokenRecuperacion();
        $this->emailService = new EmailService();
    }
    
    /**
     * Muestra el formulario de recuperación de contraseña
     */
    public function mostrarFormulario() {
        // Incluir la vista del formulario
        require_once __DIR__ . '/../templates/recupero.php';
    }
    
    /**
     * Procesa el envío del email de recuperación
     */
    public function enviarEmail() {
        // Verificar que sea una petición POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigirConMensaje('recupero.php', 'error', 'Método no permitido');
            return;
        }
        
        // Obtener y validar el email
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        
        if (empty($email)) {
            $this->redirigirConMensaje('recupero.php', 'error', 'Por favor, introduce tu correo electrónico');
            return;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirigirConMensaje('recupero.php', 'error', 'El correo electrónico no es válido');
            return;
        }
        
        // Buscar el usuario por email
        $usuario = $this->usuarioModel->buscarPorEmail($email);
        
        if (!$usuario) {
            // Por seguridad, no revelar si el email existe o no
            // Mostrar el mismo mensaje de éxito
            $this->redirigirConMensaje(
                'recupero.php',
                'success',
                'Si el correo está registrado, recibirás un enlace de recuperación'
            );
            return;
        }
        
        try {
            // Generar token único
            $token = $this->generarToken();
            
            // Guardar token en la base de datos
            $tokenCreado = $this->tokenModel->crear($usuario['id'], $token);
            
            if (!$tokenCreado) {
                throw new Exception('Error al crear el token de recuperación');
            }
            
            // Enviar email con el token
            $nombreUsuario = $usuario['nombre'] ?? $usuario['username'] ?? 'Usuario';
            $emailEnviado = $this->emailService->enviarEmailRecuperacion(
                $email,
                $nombreUsuario,
                $token
            );
            
            if (!$emailEnviado) {
                throw new Exception('Error al enviar el email');
            }
            
            // Redirigir con mensaje de éxito
            $this->redirigirConMensaje(
                'recupero.php',
                'success',
                'Se ha enviado un enlace de recuperación a tu correo electrónico'
            );
            
        } catch (Exception $e) {
            error_log("Error en recuperación de contraseña: " . $e->getMessage());
            $this->redirigirConMensaje(
                'recupero.php',
                'error',
                'Hubo un error al procesar tu solicitud. Inténtalo de nuevo más tarde'
            );
        }
    }
    
    /**
     * Muestra el formulario para ingresar la nueva contraseña
     */
    public function mostrarFormularioNuevaPassword() {
        // Obtener el token de la URL
        $token = isset($_GET['token']) ? trim($_GET['token']) : '';
        
        if (empty($token)) {
            $this->redirigirConMensaje('recupero.php', 'error', 'Token no válido');
            return;
        }
        
        // Validar el token
        $tokenValido = $this->tokenModel->validar($token);
        
        if (!$tokenValido) {
            $this->redirigirConMensaje(
                'recupero.php',
                'error',
                'El enlace de recuperación ha expirado o no es válido. Solicita uno nuevo'
            );
            return;
        }
        
        // Mostrar el formulario para nueva contraseña
        require_once __DIR__ . '/../templates/restablecer_password.php';
    }
    
    /**
     * Actualiza la contraseña del usuario
     */
    public function actualizarPassword() {
        // Verificar que sea una petición POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigirConMensaje('recupero.php', 'error', 'Método no permitido');
            return;
        }
        
        // Obtener datos del formulario
        $token = isset($_POST['token']) ? trim($_POST['token']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $passwordConfirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
        
        // Validaciones
        if (empty($token)) {
            $this->redirigirConMensaje('recupero.php', 'error', 'Token no válido');
            return;
        }
        
        if (empty($password) || empty($passwordConfirm)) {
            $this->redirigirConMensaje(
                "?controller=recuperacion&action=restablecer&token=$token",
                'error',
                'Por favor, completa todos los campos'
            );
            return;
        }
        
        if ($password !== $passwordConfirm) {
            $this->redirigirConMensaje(
                "?controller=recuperacion&action=restablecer&token=$token",
                'error',
                'Las contraseñas no coinciden'
            );
            return;
        }
        
        if (strlen($password) < 6) {
            $this->redirigirConMensaje(
                "?controller=recuperacion&action=restablecer&token=$token",
                'error',
                'La contraseña debe tener al menos 6 caracteres'
            );
            return;
        }
        
        // Validar el token nuevamente
        $tokenData = $this->tokenModel->validar($token);
        
        if (!$tokenData) {
            $this->redirigirConMensaje(
                'recupero.php',
                'error',
                'El enlace de recuperación ha expirado o no es válido'
            );
            return;
        }
        
        try {
            // Actualizar la contraseña
            $actualizado = $this->usuarioModel->actualizarPassword(
                $tokenData['usuario_id'],
                $password
            );
            
            if (!$actualizado) {
                throw new Exception('Error al actualizar la contraseña');
            }
            
            // Marcar el token como usado
            $this->tokenModel->marcarComoUsado($token);
            
            // Redirigir al login con mensaje de éxito
            $this->redirigirConMensaje(
                'login.php',
                'success',
                'Tu contraseña ha sido actualizada correctamente. Ya puedes iniciar sesión'
            );
            
        } catch (Exception $e) {
            error_log("Error al actualizar contraseña: " . $e->getMessage());
            $this->redirigirConMensaje(
                "?controller=recuperacion&action=restablecer&token=$token",
                'error',
                'Hubo un error al actualizar tu contraseña. Inténtalo de nuevo'
            );
        }
    }
    
    /**
     * Genera un token único y seguro
     * 
     * @return string Token de 64 caracteres
     */
    private function generarToken() {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Redirige con un mensaje en la sesión
     * 
     * @param string $url URL de destino
     * @param string $tipo Tipo de mensaje (success, error, warning)
     * @param string $mensaje Mensaje a mostrar
     */
    private function redirigirConMensaje($url, $tipo, $mensaje) {
        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Guardar mensaje en sesión
        $_SESSION['mensaje'] = $mensaje;
        $_SESSION['mensaje_tipo'] = $tipo;
        
        // Redirigir
        header("Location: $url");
        exit();
    }
}
