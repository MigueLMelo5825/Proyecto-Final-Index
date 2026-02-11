<?php

class AdminController
{
    private $session;
    private $usuarioModel;

    public function __construct($session)
    {
        $this->session = $session;
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        // Cargar todos los usuarios
        $usuarios = $this->usuarioModel->getAllUsuarios();

        require __DIR__ . '/../templates/panelAdmin.php';
    }

    public function cambiarRol()
    {
        if (!isset($_GET['id'])) {
            die("ID de usuario no proporcionado");
        }

        $id = $_GET['id'];
        $usuario = $this->usuarioModel->getUsuarioById($id);

        require __DIR__ . '/../templates/cambiarRol.php';
    }

    public function guardarRol()
    {
        $id = $_POST['id'];
        $nivel = $_POST['nivel'];

        $this->usuarioModel->actualizarNivel($id, $nivel);

        header("Location: index.php?ctl=panelAdmin");
        exit;
    }

    public function eliminarUsuario()
    {
        if (!isset($_GET['id'])) {
            die("ID de usuario no proporcionado");
        }

        $id = $_GET['id'];

        $this->usuarioModel->eliminarUsuario($id);

        header("Location: index.php?ctl=panelAdmin");
        exit;
    }
}
