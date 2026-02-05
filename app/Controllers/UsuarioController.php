<?php

class UsuarioController {

    private $session;
    private $usuarioModel;

    public function __construct($session) {
        $this->session = $session;
        $this->usuarioModel = new UsuarioModel();
    }

    // -------------------------------------------------------------
    // REGISTRO
    // -------------------------------------------------------------
    public function registro() {

    // 1. Conexión a la BD
    $pdo = Database::getConnection();

    // 2. Cargar países para el select
    $paises = $pdo->query("SELECT * FROM paises ORDER BY nombre")->fetchAll();

    // 3. Si NO es POST → mostrar formulario
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        require __DIR__ . '/../templates/registro.php';
        return;
    }

    // 4. Recoger datos del formulario
    $nombre = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $pais_id = $_POST['pais_id'] ?? null;

    // 5. Validaciones
    if ($password !== $password2) {
        echo "<h2>Las contraseñas no coinciden</h2>";
        return;
    }

    // 6. Encriptar contraseña
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // 7. Guardar en BD
    $this->usuarioModel->registrar($nombre, $email, $hash, $pais_id);

    // 8. Redirigir al login
    header("Location: index.php?ctl=login");
    exit;
}

    // -------------------------------------------------------------
    // LOGIN
    // -------------------------------------------------------------
   public function login() {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        require __DIR__ . '/../templates/login.php';
        return;
    }

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $usuario = $this->usuarioModel->validarLogin($email, $password);

    if ($usuario) {

        $rol = $usuario['rol'];
        $nivel = ($rol === 'admin') ? 3 : 1;

        // Guardar sesión de forma compatible con SessionManager
        $_SESSION['usuarioId']     = $usuario['id'];      // usado por getUserId()
        $_SESSION['usuarioNombre'] = $usuario['nombre'];  // usado por getUserName()
        $_SESSION['usuarioNivel']  = $nivel;              // usado por getUserLevel()

        // Guardar también lo que espera isLoggedIn()
        $_SESSION['id_usuario'] = $usuario['id'];

        header("Location: index.php?ctl=perfil");
        exit;
    }

    echo "<h2>Credenciales incorrectas</h2>";
}


    // -------------------------------------------------------------
    // PERFIL
    // -------------------------------------------------------------
  public function perfil() {


        // Obtener ID del usuario directamente de la sesión
        $idUsuario = $_SESSION['id_usuario'] ?? null;

        if (!$idUsuario) {
        echo "<h2>Error: no se pudo obtener el usuario actual.</h2>";
        return;
    }

    // 1. Cargar listas del usuario
    $listas = ListaModel::obtenerListasUsuario($idUsuario);

    // 2. Cargar top libros y películas
    $librosModel = new Libros();
    $pelisModel  = new Peliculas();

    $topLibros = $librosModel->obtenerTopLibros();
    $topPeliculas = $pelisModel->obtenerTopPeliculas();

    foreach ($topPeliculas as &$p) {
        $p['genero_nombre'] = $pelisModel->obtenerNombreGenero($p['genero']);
    }

    // 3. Cargar la vista
    require __DIR__ . '/../templates/perfil.php';
}




}
