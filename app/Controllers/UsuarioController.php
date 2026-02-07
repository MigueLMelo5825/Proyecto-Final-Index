<?php

class UsuarioController
{
    private $session;
    private $usuarioModel;

    public function __construct($session)
    {
        $this->session      = $session;
        $this->usuarioModel = new UsuarioModel();
    }

    // -------------------------------------------------------------
    // REGISTRO
    // -------------------------------------------------------------
    public function registro()
    {
        $pdo     = Database::getConnection();
        $paises  = $pdo->query("SELECT * FROM paises ORDER BY nombre")->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require __DIR__ . '/../templates/registro.php';
            return;
        }

        $nombre    = $_POST['name']     ?? '';
        $email     = $_POST['email']    ?? '';
        $password  = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';
        $pais_id   = $_POST['pais_id']  ?? null;

        if ($password !== $password2) {
            echo "<h2>Las contraseñas no coinciden</h2>";
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $this->usuarioModel->registrar($nombre, $email, $hash, $pais_id);

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

        $email    = $_POST['email']    ?? '';
        $password = $_POST['password'] ?? '';

        $usuario = $this->usuarioModel->validarLogin($email, $password);

        if ($usuario) {

            $rol   = $usuario['rol'] ?? 'user';
            $nivel = ($rol === 'admin') ? 3 : 1;

            // LOGIN OFICIAL
            $this->session->login($usuario['id'], $usuario['nombre'], $nivel);

            // Compatibilidad con código antiguo
            $_SESSION['id_usuario'] = $usuario['id'];

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

    // Conexión
    $conexion = Database::getConnection();

    // Listas del usuario
    $listas = ListaModel::obtenerListasUsuario($conexion, $idUsuario);
    $numeroListas = count($listas);

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



    // -------------------------------------------------------------
    // RECUPERAR CONTRASEÑA
    // -------------------------------------------------------------
    public function recuperar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require __DIR__ . '/../templates/recupero.php';
            return;
        }

        $email = trim($_POST['email'] ?? '');

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<h2>Email inválido</h2>";
            return;
        }

        $usuario = $this->usuarioModel->buscarPorEmail($email);

        if (!$usuario) {
            echo "<h2>Si el correo existe, recibirás un enlace para recuperar la contraseña.</h2>";
            return;
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION['reset_email']  = $email;
        $_SESSION['reset_token']  = password_hash($token, PASSWORD_DEFAULT);
        $_SESSION['reset_expira'] = time() + 3600;

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

        $link = "{$scheme}://{$host}{$base}/index.php?ctl=reset&email=" . urlencode($email) . "&token=" . urlencode($token);

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

    // -------------------------------------------------------------
    // RESET CONTRASEÑA
    // -------------------------------------------------------------
    public function reset()
    {
        $email = $_GET['email'] ?? '';
        $token = $_GET['token'] ?? '';

        if ($email === '' || $token === '') {
            echo "<h2>Enlace inválido</h2>";
            return;
        }

        if (
            empty($_SESSION['reset_email']) ||
            empty($_SESSION['reset_token']) ||
            empty($_SESSION['reset_expira'])
        ) {
            echo "<h2>La sesión de recuperación ha expirado</h2>";
            return;
        }

        if ($email !== $_SESSION['reset_email']) {
            echo "<h2>Email no válido</h2>";
            return;
        }

        if (time() > $_SESSION['reset_expira']) {
            unset($_SESSION['reset_email'], $_SESSION['reset_token'], $_SESSION['reset_expira']);
            echo "<h2>El enlace ha expirado</h2>";
            return;
        }

        if (!password_verify($token, $_SESSION['reset_token'])) {
            echo "<h2>Token inválido</h2>";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require __DIR__ . '/../templates/restablecer_contrasena.php';
            return;
        }

        $password  = $_POST['password']  ?? '';
        $password2 = $_POST['password2'] ?? '';

        if ($password === '' || $password !== $password2) {
            echo "<h2>Las contraseñas no coinciden</h2>";
            return;
        }

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

        unset($_SESSION['reset_email'], $_SESSION['reset_token'], $_SESSION['reset_expira']);

        echo "<h2>Contraseña actualizada correctamente</h2>";
        echo "<a href='index.php?ctl=login'>Iniciar sesión</a>";
    }
}
