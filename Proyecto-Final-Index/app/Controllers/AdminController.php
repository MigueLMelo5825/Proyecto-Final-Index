<?php

/**
 * Controlador de administración
 * Gestiona usuarios, roles y cualquier funcionalidad reservada a administradores.
 */
class AdminController {

    private $session;
    private $usuarioModel;

    public function __construct($session) {
        $this->session = $session;
        $this->usuarioModel = new UsuarioModel();
    }

    // -------------------------------------------------------------
    // PANEL PRINCIPAL DEL ADMIN
    // -------------------------------------------------------------
    public function index() {

        // Obtener todos los usuarios para mostrarlos en el panel
        $usuarios = $this->usuarioModel->obtenerTodos();

        require __DIR__ . '/../templates/panelAdmin.php';
    }

    // -------------------------------------------------------------
    // CAMBIAR ROL DE UN USUARIO
    // -------------------------------------------------------------
    public function cambiarRol() {

        $id = $_POST['id_usuario'] ?? null;
        $rol = $_POST['rol'] ?? null;

        if ($id && $rol) {
            $this->usuarioModel->actualizarRol($id, $rol);
        }

        header("Location: index.php?ctl=panelAdmin");
        exit;
    }

    // -------------------------------------------------------------
    // ELIMINAR USUARIO (opcional, por si lo quieres añadir)
    // -------------------------------------------------------------
    public function eliminarUsuario() {

    $id = $_GET['id'] ?? null;

    if ($id) {
        $this->usuarioModel->eliminar($id);
    }

    header("Location: index.php?ctl=panelAdmin");
    exit;
}

}
