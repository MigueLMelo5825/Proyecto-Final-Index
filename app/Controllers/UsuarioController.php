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

        $idUSer = $this->usuarioModel->registrar($nombre, $email, $hash, $pais_id);

        if (!$idUSer) {
            echo "<h2>No se pudo registrar el usuario</h2>";
            return;
        }

        $eventoModel = new EventoModel();
        $eventoModel->registrarEvento(
            $idUSer,
            "registro",
            "Nuevo usuario registrado",
            "El usuario se ha registrado en la plataforma"
        );



        $token = bin2hex(random_bytes(32));
        $validoHasta = time() + 3600; // 1 hora de validez

        $okToken = $this->usuarioModel->guardarTokenActivacion($idUSer, $token, $validoHasta);

        if (!$okToken) {
            echo "<h2>No se pudo guardar el token de activación</h2>";
            return;
        }

        // Crear enlace de activación
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

        $link = "{$scheme}://{$host}{$base}/index.php?ctl=activar&token=" . urlencode($token);

        // correo de activación
        $subject = "Activa tu cuenta";
        $htmlBody = "
            <h2>Activación de cuenta</h2>
            <p>Hola " . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . ",</p>
            <p>Gracias por registrarte. Para activar tu cuenta, haz clic en el siguiente enlace:</p>
            <p><a href='{$link}'>{$link}</a></p>
            <p>Este enlace es válido por 1 hora.</p>";
        $altBody = "Activa tu cuenta: {$link} (válido por 1 hora)";

        $enviado = Mailer::enviar($email, $nombre, $subject, $htmlBody, $altBody);

        if (!$enviado) {
            echo "<h2>No se pudo enviar el correo de activación. Revisa la configuración SMTP.</h2>";
            return;
        }

        echo "<h2>Registro exitoso. Revisa tu correo para activar tu cuenta.</h2>";
        echo "<a href='index.php?ctl=login'>Iniciar sesión</a>";

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
        //var_dump($usuario); // Depuración: muestra el resultado de validarLogin
        //exit;

        if ($usuario && !isset($usuario['__inactivo__'])) {

            $rol   = $usuario['rol'] ?? 'user';
            $nivel = ($rol === 'admin') ? 3 : 1;
            //$activo = $usuario['activo'] ? 0 : 1;

            // LOGIN OFICIAL
            $this->session->login($usuario['id'], $usuario['nombre'], $nivel);

            // Compatibilidad con código antiguo
            $_SESSION['id_usuario'] = $usuario['id'];

            CookieManager::set('usuarioNombre', $usuario['nombre']);

            header("Location: index.php?ctl=perfil");
            exit;
        }

        if (is_array($usuario) && isset($usuario['__inactivo__'])) {
            $_SESSION['swal'] = [
                'title' => 'Cuenta inactiva',
                'text'  => 'Tu cuenta no está activa. Revisa tu correo para activar tu cuenta.',
                'icon'  => 'warning'
            ];
            header("Location: index.php?ctl=login");
            exit;
        }

        $_SESSION['swal'] = [
            'title' => 'Error de login',
            'text'  => 'Contraseña incorrecta. Inténtalo de nuevo.',
            'icon'  => 'error'
        ];
        header("Location: index.php?ctl=login");
        exit;

        /*if (isset($usuario['__inactivo__'])) {
            echo "<h2>La cuenta no está activa. Por favor, revisa tu correo para activar tu cuenta.</h2>";
            return;
        }

        echo "<h2>Credenciales incorrectas</h2>";*/
    }

    // -------------------------------------------------------------
    // PERFIL
    // -------------------------------------------------------------
    public function perfil()
    {
        $idUsuario = $_SESSION['id_usuario'] ?? null;

        if (!$idUsuario) {
            header("Location: index.php?ctl=login");
            exit;
        }

        // ID del perfil que se está visitando
        $idPerfil = $_GET['id'] ?? $idUsuario;

        // Obtener usuario del perfil
        $usuario = $this->usuarioModel->obtenerPorId($idPerfil);

        // Si falla la consulta, evitar warnings
        if (!$usuario || !is_array($usuario)) {
            $usuario = [
                'nombre' => 'Usuario desconocido',
                'bio' => '',
                'foto' => 'web/img/default.png',
                'top_libros' => '[]',
                'top_peliculas' => '[]'
            ];
        }

        // Cargar top libros
        $librosModel = new Libros();
        $topLibrosIds = json_decode($usuario['top_libros'] ?? '[]', true);
        $topLibros = $librosModel->obtenerPorIds($topLibrosIds);

        // Cargar top películas
        $pelisModel = new Peliculas();
        $topPeliculasIds = json_decode($usuario['top_peliculas'] ?? '[]', true);
        $topPeliculas = $pelisModel->obtenerPorIds($topPeliculasIds);

        // Cargar listas
        $listasModel = new ListaModel();
        $listas = $listasModel->obtenerListasPorUsuario($idPerfil);
        $numeroListas = count($listas);

        // -------------------------------
        // NUEVO: sistema de seguidores
        // -------------------------------
        $seguidorModel = new SeguidorModel();

        // ¿El usuario logueado sigue a este perfil?
        $esSeguidor = $seguidorModel->esSeguidor($idUsuario, $idPerfil);

        // Listas de seguidores y seguidos
        $seguidores = $seguidorModel->obtenerSeguidores($idPerfil);
        $seguidos   = $seguidorModel->obtenerSeguidos($idPerfil);

        require __DIR__ . '/../templates/perfil.php';
    }

    public function verSeguidores()
    {
        $idPerfil = $_GET['id'] ?? null;
        if (!$idPerfil) {
            header("Location: index.php?ctl=inicio");
            exit;
        }

        $seguidorModel = new SeguidorModel();
        $seguidores = $seguidorModel->obtenerSeguidores($idPerfil);

        require __DIR__ . '/../templates/seguidores.php';
    }

    public function verSeguidos()
    {
        $idPerfil = $_GET['id'] ?? null;
        if (!$idPerfil) {
            header("Location: index.php?ctl=inicio");
            exit;
        }

        $seguidorModel = new SeguidorModel();
        $seguidos = $seguidorModel->obtenerSeguidos($idPerfil);

        require __DIR__ . '/../templates/seguidos.php';
    }


    ///////////////////////////////////////////

    public function buscarUsuarios()
    {
        $termino = $_GET['email'] ?? '';

        $usuarios = [];
        if ($termino !== '') {
            $usuarios = $this->usuarioModel->buscarUsuariosPorEmailParcial($termino);
        }

        require __DIR__ . '/../templates/buscar_usuarios.php';
    }


    // RECUPERAR CONTRASEÑA

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
        $validoHasta = time() + 3600; // 1 hora de validez

        $this->usuarioModel->guardarTokenRecuperacion($usuario['id'], $token, $validoHasta);

        /*$_SESSION['reset_email']  = $email;
        $_SESSION['reset_token']  = password_hash($token, PASSWORD_DEFAULT);
        $_SESSION['reset_expira'] = time() + 3600;*/

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


    // ACTIVAR CUENTA
    public function activar()
    {
        $token = $_GET['token'] ?? '';

        if ($token === '') {
            echo "<h2>Enlace de activación inválido</h2>";
            return;
        }

        $row = $this->usuarioModel->buscarTokenValido($token);

        if (!$row) {
            echo "<h2>Enlace de activación inválido o expirado</h2>";
            return;
        }

        $idUser = $row['id_user'];

        $ok = $this->usuarioModel->activarUsuario($idUser);
        if (!$ok) {
            echo "<h2>No se pudo activar la cuenta</h2>";
            return;
        }

        $this->usuarioModel->borrarToken($row['id']);


        require_once __DIR__ . './../templates/activar_cuenta.php';
    }

    // RESET CONTRASEÑA

    public function reset()
    {
        $email = $_GET['email'] ?? '';
        $token = $_GET['token'] ?? '';

        if ($email === '' || $token === '') {
            echo "<h2>Enlace inválido</h2>";
            return;
        }

        // 1. Buscar token en BD
        $row = $this->usuarioModel->buscarTokenRecuperacion($token);

        if (!$row) {
            echo "<h2>Enlace inválido o expirado</h2>";
            return;
        }

        // 2. Obtener usuario desde el token
        $usuario = $this->usuarioModel->obtenerPorId($row['id_user']);

        if (!$usuario || $usuario['email'] !== $email) {
            echo "<h2>Usuario no válido</h2>";
            return;
        }

        // 3. Mostrar formulario si es GET
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require __DIR__ . '/../templates/restablecer_contrasena.php';
            return;
        }

        // 4. Procesar cambio de contraseña
        $password  = $_POST['password']  ?? '';
        $password2 = $_POST['password2'] ?? '';

        if ($password === '' || $password !== $password2) {
            echo "<h2>Las contraseñas no coinciden</h2>";
            return;
        }

        $ok = $this->usuarioModel->actualizarPassword($usuario['id'], $password);

        if (!$ok) {
            echo "<h2>No se pudo actualizar la contraseña</h2>";
            return;
        }

        // 5. Invalidar token 
        $this->usuarioModel->borrarTokenRecuperacion($token);

        require __DIR__ . '/../templates/contrasena_modificado.php';
    }
    public function logout()
    {
        $this->session->logout();
        header("Location: index.php?ctl=login");
        exit;
    }
    public function ajustesPerfil()
    {
        $idUsuario = $_SESSION['id_usuario'] ?? null;

        if (!$idUsuario) {
            header("Location: index.php?ctl=login");
            exit;
        }

        // Obtener usuario
        $usuario = $this->usuarioModel->obtenerPorId($idUsuario);

        // Asegurar claves
        $usuario['bio']  = $usuario['bio']  ?? '';
        $usuario['foto'] = $usuario['foto'] ?? 'web/img/default.png';

        // Modelos correctos (BD, NO API)
        $librosModel = new Libros();
        $pelisModel  = new Peliculas();

        // OBLIGATORIO: SOLO BD
        $todosLibros = $librosModel->obtenerTodos();
        $todasPeliculas = $pelisModel->obtenerTodas();

        // Convertir JSON a array
        $topLibrosIds = json_decode($usuario['top_libros'] ?? '[]', true);
        $topPeliculasIds = json_decode($usuario['top_peliculas'] ?? '[]', true);

        if (!is_array($topLibrosIds)) $topLibrosIds = [];
        if (!is_array($topPeliculasIds)) $topPeliculasIds = [];

        require __DIR__ . '/../templates/ajustes_perfil.php';
    }
    // -------------------------------------------------------------
    // SEGUIR A UN USUARIO
    // -------------------------------------------------------------
    public function seguir()
    {
        $idSeguido = $_GET['id'] ?? null;
        $idSeguidor = $_SESSION['id_usuario'] ?? null;

        if (!$idSeguidor || !$idSeguido || $idSeguidor == $idSeguido) {
            header("Location: index.php?ctl=perfil&id=$idSeguido");
            exit;
        }

        $seguidorModel = new SeguidorModel();
        $seguidorModel->seguir($idSeguidor, $idSeguido);

        header("Location: index.php?ctl=perfil&id=$idSeguido");
        exit;
    }

    // -------------------------------------------------------------
    // DEJAR DE SEGUIR
    // -------------------------------------------------------------
    public function dejarSeguir()
    {
        $idSeguido = $_GET['id'] ?? null;
        $idSeguidor = $_SESSION['id_usuario'] ?? null;

        if (!$idSeguidor || !$idSeguido || $idSeguidor == $idSeguido) {
            header("Location: index.php?ctl=perfil&id=$idSeguido");
            exit;
        }

        $seguidorModel = new SeguidorModel();
        $seguidorModel->dejarDeSeguir($idSeguidor, $idSeguido);

        header("Location: index.php?ctl=perfil&id=$idSeguido");
        exit;
    }


    /////////////////////////////////////////////////////////

    public function guardarFotoPerfil()
    {
        $idUsuario = $_SESSION['id_usuario'];

        if (!empty($_FILES['foto']['tmp_name'])) {
            $nombre = 'foto_' . $idUsuario . '.jpg';
            move_uploaded_file($_FILES['foto']['tmp_name'], "web/img/perfil/$nombre");

            $this->usuarioModel->actualizarFoto($idUsuario, "web/img/perfil/$nombre");
        }

        header("Location: index.php?ctl=perfil");
    }

    public function guardarBio()
    {
        $idUsuario = $_SESSION['id_usuario'];
        $bio = $_POST['bio'] ?? '';

        $this->usuarioModel->actualizarBio($idUsuario, $bio);

        header("Location: index.php?ctl=perfil");
    }

    ///////////////////////////////////////////////////////

    public function guardarTopLibros()
    {
        $idUsuario = $_SESSION['id_usuario'];

        $seleccion = $_POST['top_libros'] ?? [];
        $seleccion = array_slice($seleccion, 0, 4);

        $this->usuarioModel->actualizarTopLibros($idUsuario, json_encode($seleccion));

        header("Location: index.php?ctl=perfil");
    }


    ////////////////////////////////////////////////////////////////////
    public function guardarTopPeliculas()
    {
        $idUsuario = $_SESSION['id_usuario'];
        $seleccion = $_POST['top_peliculas'] ?? [];
        $seleccion = array_slice($seleccion, 0, 4);

        $this->usuarioModel->actualizarTopPeliculas($idUsuario, json_encode($seleccion));

        header("Location: index.php?ctl=perfil");
    }
}
