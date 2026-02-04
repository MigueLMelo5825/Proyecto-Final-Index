<?php

class UsuarioController {

    private $session;
    private $usuarioModel;

    public function __construct($session) {
        $this->session = $session;
        $this->usuarioModel = new UsuarioModel();
    }

    // -------------------------------------------------------------
    // LOGIN
    // -------------------------------------------------------------
    public function login() {

        $email = $_POST['email'] ?? '';
        $password = $_POST['contrasena'] ?? '';

        $usuario = $this->usuarioModel->validarLogin($email, $password);

        if ($usuario) {

            // Rol textual: 'admin' o 'usuario'
            $rol = $usuario['rol'];

            // Convertir rol textual a nivel numérico
            $nivel = ($rol === 'admin') ? 3 : 1;

            // Guardar en sesión
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
