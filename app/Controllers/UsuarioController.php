<?php

//use App\Core\Mailer;

class UsuarioController
{
    private $session;
    private $usuarioModel;

    public function __construct($session)
    {
        $this->session = $session;
        $this->usuarioModel = new UsuarioModel();
    }

    // -------------------------------------------------------------
    // REGISTRO
    // -------------------------------------------------------------
    public function registro()
    {
        // 1) Conexión BD + países para el select
        $pdo = Database::getConnection();
        $paises = $pdo->query("SELECT * FROM paises ORDER BY nombre")->fetchAll();

        // 2) Si no es POST -> mostrar formulario
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require __DIR__ . '/../templates/registro.php';
            return;
        }

        // 3) Recoger datos
        $nombre    = $_POST['name'] ?? '';
        $email     = $_POST['email'] ?? '';
        $password  = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';
        $pais_id   = $_POST['pais_id'] ?? null;

        // 4) Validaciones
        if ($password !== $password2) {
            echo "<h2>Las contraseñas no coinciden</h2>";
            return;
        }

        // 5) Hash
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // 6) Guardar
        $this->usuarioModel->registrar($nombre, $email, $hash, $pais_id);

        // 7) Redirigir
        header("Location: index.php?ctl=login");
        exit;
    }

    // -------------------------------------------------------------
    // LOGIN
    // -------------------------------------------------------------
    public function login()
    {
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require __DIR__ . '/../templates/login.php';
            return;
        }

        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $usuario = $this->usuarioModel->validarLogin($email, $password);

        if ($usuario) {
            $rol   = $usuario['rol'] ?? 'user';
            $nivel = ($rol === 'admin') ? 3 : 1;

            // 1) Compatibilidad con SessionManager (si usas $this->session->get/set)
            if ($this->session) {
                $this->session->set('id_usuario', $usuario['id']);
                $this->session->set('nombre', $usuario['nombre']);
                $this->session->set('rol', $rol);
                $this->session->set('nivel', $nivel);
            }

            // 2) Compatibilidad con código que lee directamente $_SESSION
            $_SESSION['id_usuario']      = $usuario['id'];
            $_SESSION['usuarioId']       = $usuario['id'];
            //$_SESSION['usuarioNombre']   = $usuario['nombre'];
            $_SESSION['usuarioNivel']    = $nivel;

            CookieManager::set('usuarioNombre', $usuario['nombre']);

            header("Location: index.php?ctl=perfil");
            exit;
        }

        echo "<h2>Credenciales incorrectas</h2>";
    }

    // -------------------------------------------------------------
    // PERFIL
    // -------------------------------------------------------------
    public function perfil()
    {
        $idUsuario = $_SESSION['id_usuario'] ?? null;

        if (!$idUsuario) {
            echo "<h2>Error: no se pudo obtener el usuario actual.</h2>";
            return;
        }

        // Listas del usuario
        $listas = ListaModel::obtenerListasUsuario($idUsuario);

        // Tops
        $librosModel = new Libros();
        $pelisModel  = new Peliculas();

        $topLibros    = $librosModel->obtenerTopLibros();
        $topPeliculas = $pelisModel->obtenerTopPeliculas();

        foreach ($topPeliculas as &$p) {
            $p['genero_nombre'] = $pelisModel->obtenerNombreGenero($p['genero']);
        }
        unset($p);

        require __DIR__ . '/../templates/perfil.php';
    }

    public function recuperar()
    {
        // Mostrar formulario si no es POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require __DIR__ . '/../templates/recupero.php';
            return;
        }

        $email = trim($_POST['email'] ?? '');

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<h2>Email inválido</h2>";
            return;
        }

        // Buscar usuario (esto sí existe en tu UsuarioModel)
        $usuario = $this->usuarioModel->buscarPorEmail($email);

        // Respuesta neutra (seguridad: no decir si existe o no)
        if (!$usuario) {
            echo "<h2>Si el correo existe, recibirás un enlace para recuperar la contraseña.</h2>";
            return;
        }

        // Generar token y guardar en sesión (sin BD)
        $token = bin2hex(random_bytes(32));
        $_SESSION['reset_email']  = $email;
        $_SESSION['reset_token']  = password_hash($token, PASSWORD_DEFAULT);
        $_SESSION['reset_expira'] = time() + 3600; // 1 hora

        // Construir URL de reset
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

        $link = "{$scheme}://{$host}{$base}/index.php?ctl=reset&email=" . urlencode($email) . "&token=" . urlencode($token);

        // Enviar correo con PHPMailer (Gmail) usando tu Mailer
        $subject = "Recuperar contraseña";
        $htmlBody = "
            <h2>Recuperación de contraseña</h2>
            <p>Hola " . htmlspecialchars($usuario['nombre'] ?? 'Usuario', ENT_QUOTES, 'UTF-8') . ",</p>
            <p>Has solicitado recuperar tu contraseña.</p>
            <p>Haz clic en este enlace para crear una nueva (caduca en 1 hora):</p>
            <p><a href='{$link}'>{$link}</a></p>
            <p>Si no lo solicitaste, ignora este mensaje.</p>
        ";

        $altBody = "Recuperación de contraseña (caduca en 1 hora): {$link}";

        $enviado = Mailer::enviar($email, $usuario['nombre'] ?? 'Usuario', $subject, $htmlBody, $altBody);

        if (!$enviado) {
            echo "<h2>No se pudo enviar el correo. Revisa la configuración SMTP.</h2>";
            return;
        }

        echo "<h2>Si el correo existe, recibirás un enlace para recuperar la contraseña.</h2>";
    }

    public function reset()
    {
        // 1) Comprobar parámetros
        $email = $_GET['email'] ?? '';
        $token = $_GET['token'] ?? '';

        if ($email === '' || $token === '') {
            echo "<h2>Enlace inválido</h2>";
            return;
        }

        // 2) Comprobar sesión
        if (
            empty($_SESSION['reset_email']) ||
            empty($_SESSION['reset_token']) ||
            empty($_SESSION['reset_expira'])
        ) {
            echo "<h2>La sesión de recuperación ha expirado</h2>";
            return;
        }

        // 3) Validar email
        if ($email !== $_SESSION['reset_email']) {
            echo "<h2>Email no válido</h2>";
            return;
        }

        // 4) Comprobar expiración
        if (time() > $_SESSION['reset_expira']) {
            unset($_SESSION['reset_email'], $_SESSION['reset_token'], $_SESSION['reset_expira']);
            echo "<h2>El enlace ha expirado</h2>";
            return;
        }

        // 5) Verificar token
        if (!password_verify($token, $_SESSION['reset_token'])) {
            echo "<h2>Token inválido</h2>";
            return;
        }

        // 6) Si no es POST → mostrar formulario
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require __DIR__ . '/../templates/restablecer_contrasena.php';
            return;
        }

        // 7) Procesar nueva contraseña
        $password  = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        if ($password === '' || $password !== $password2) {
            echo "<h2>Las contraseñas no coinciden</h2>";
            return;
        }

        // 8) Actualizar contraseña
        $usuario = $this->usuarioModel->buscarPorEmail($email);

        if (!$usuario) {
            echo "<h2>Error de usuario</h2>";
            return;
        }

        $ok = $this->usuarioModel->actualizarPassword($usuario['id'], $password);

        if (!$ok) {
            echo "<h2>No se pudo actualizar la contraseña</h2>";
            return;
        }

        // 9) Limpiar sesión de recuperación
        unset($_SESSION['reset_email'], $_SESSION['reset_token'], $_SESSION['reset_expira']);

        echo "<h2>Contraseña actualizada correctamente</h2>";
        echo "<a href='index.php?ctl=login'>Iniciar sesión</a>";
    }


}
