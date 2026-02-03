<?php

class UsuarioModel {

    private $db;

    public function __construct() {
        $this->db = Conexion::getConexion();
    }

    // -------------------------------------------------------------
    // VALIDAR LOGIN
    // -------------------------------------------------------------
    public function validarLogin($email, $password) {

        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            return false;
        }

        // Verificar contraseña
        if (!password_verify($password, $usuario['contrasena'])) {
            return false;
        }

        return $usuario;
    }
}
    