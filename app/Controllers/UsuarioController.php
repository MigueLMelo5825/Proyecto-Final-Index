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
}
