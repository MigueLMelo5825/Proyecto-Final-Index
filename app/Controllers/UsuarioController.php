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

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require __DIR__ . '/../templates/registro.php';
            return;
        }

        // Recoger datos
        $nombre = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        // Validaciones básicas
        if ($password !== $password2) {
            echo "<h2>Las contraseñas no coinciden</h2>";
            return;
        }

        // Encriptar contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Guardar en BD
        $this->usuarioModel->registrar($nombre, $email, $hash);

        // Redirigir al login
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
        $password = $_POST['password'] ?? '';   // ← CORREGIDO

        $usuario = $this->usuarioModel->validarLogin($email, $password);

        if ($usuario) {

            $rol = $usuario['rol'];
            $nivel = ($rol === 'admin') ? 3 : 1;

            $this->session->set('id_usuario', $usuario['id']);
            $this->session->set('nombre', $usuario['nombre']);
            $this->session->set('rol', $rol);
            $this->session->set('nivel', $nivel);

            header("Location: index.php?ctl=perfil");
            exit;
        }

        echo "<h2>Credenciales incorrectas</h2>";
    }

    // -------------------------------------------------------------
    // PERFIL
    // -------------------------------------------------------------
    public function perfil() {

        $librosModel = new Libros();
        $pelisModel  = new Peliculas();

        $topLibros = $librosModel->obtenerTopLibros();
        $topPeliculas = $pelisModel->obtenerTopPeliculas();

        foreach ($topPeliculas as &$p) {
            $p['genero_nombre'] = $pelisModel->obtenerNombreGenero($p['genero']);
        }

        require __DIR__ . '/../templates/perfil.php';
    }




function registro()
{

    $errores = [];

    $name     = recoge("name");
    $email    = recoge("email");
    $password = recoge("password");
    $password2 = recoge("password2");
    $pais = recoge("pais");


    cTexto($name, "name", $errores, 30, 2, true, true);


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores["email"] = "El correo no es válido";
    }

    
    if (strlen($password) < 6) {
        $errores["password"] = "La contraseña debe tener al menos 6 caracteres";
    }

   
    if ($password !== $password2) {
        $errores["password2"] = "Las contraseñas no coinciden";
    }

    // Si hay errores → volver a la vista
    if (!empty($errores)) {
        $params = [
            "errores" => $errores,
            "name" => $name,
            "email" => $email,
            "pais" => $pais
        ];
        require "app/templates/registro.php";
        return;
    }

    // Si todo está bien → encriptar contraseña
    $passwordHash = crypt_blowfish($password);

    // Guardar en BD 
    $db = new PDO("mysql:host=localhost;dbname=indexproyecto;charset=utf8", "root", "");
    $stmt = $db->prepare("INSERT INTO usuarios (nombre, email, contrasena,pais) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $passwordHash]);


    // Redirigir o mostrar mensaje
    header("Location: index.php?ctl=login");
}

}
