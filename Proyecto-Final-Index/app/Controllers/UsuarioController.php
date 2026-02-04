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

        //echo "Llego aqui";
        //echo __FILE__;
        //exit;

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require __DIR__ . '/../templates/login.php';
            return;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        //echo ("Email: $email, Password: $password");
        //exit;

        

        $usuario = $this->usuarioModel->validarLogin($email, $password);

        /*echo "Llego al POST";
        var_dump($usuario);
        exit;*/

        if ($usuario) {

            // Rol textual: 'admin' o 'usuario'
            $rol = $usuario['rol'];

            // Convertir rol textual a nivel numérico
            $nivel = ($rol === 'admin') ? 3 : 1;

            // Guardar en sesión
            $this->session->login(
                $usuario['id'],
                $usuario['nombre'],
                $nivel
            );

            $_SESSION['rol'] = $rol;


           /* $this->session->set('id_usuario', $usuario['id']);
            $this->session->set('nombre', $usuario['nombre']);
            $this->session->set('rol', $rol);
            $this->session->set('nivel', $nivel);*/

            header("Location: index.php?ctl=perfil");
            exit;
        }

        echo "<h2>Credenciales incorrectas</h2>";
    }

    // -------------------------------------------------------------
    // PERFIL
    // -------------------------------------------------------------
    public function perfil() {

    /*echo "Llego al perfil";
    exit;*/

        $librosModel = new Libros();
        $pelisModel  = new Peliculas();

        $topLibros = $librosModel->obtenerTopLibros();
        $topPeliculas = $pelisModel->obtenerTopPeliculas();

        foreach ($topPeliculas as &$p) {
            $p['genero_nombre'] = $pelisModel->obtenerNombreGenero($p['genero']);
        }

        require __DIR__ . '/../templates/perfil.php';
    }




    public function registro()
    {
        $errores = [];

        $name     = recoge("name");
        $email    = recoge("email");
        $password = recoge("password");
        $password2 = recoge("password2");
        $pais = recoge("pais");


        //cTexto($name, "name", $errores, 30, 2, true, true);


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
                "email" => $email
            ];
            //echo '<pre>';
            //print_r($params);
            //echo '</pre>';
            //echo "Hay errores";
            //exit;
            //require "app/templates/registro.php";
            return;
        }

        // Si todo está bien → encriptar contraseña
        // MAL -> $passwordHash = crypt_blowfish($password);
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Guardar en BD 
        $db = new PDO("mysql:host=localhost;dbname=indexproyecto;charset=utf8", "root", "");
        $stmt = $db->prepare("INSERT INTO usuarios (nombre, email, contrasena, pais) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $passwordHash, $pais]);


        // Redirigir o mostrar mensaje
        header("Location: index.php?ctl=login");
    }


}
