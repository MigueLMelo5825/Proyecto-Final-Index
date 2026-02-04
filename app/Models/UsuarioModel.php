<?php

class UsuarioModel {

    private $db;

    public function __construct() {
        $this->db = Conexion::getConexion();
    }
    // -------------------------------------------------------------
    // REGISTRAR USUARIO
    // -------------------------------------------------------------
public function registrar($nombre, $email, $hash) {
    $sql = "INSERT INTO usuarios (nombre, email, contrasena, rol, nivel)
            VALUES (?, ?, ?, 'usuario', 1)";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$nombre, $email, $hash]);
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

    if (!password_verify($password, $usuario['contrasena'])) {
        return false;
    }

    return $usuario;
}

// -------------------------------------------------------------
    // ELIMINAR USUARIO (admin)
    // -------------------------------------------------------------
    public function eliminar($id) {
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$id]);
}
// -------------------------------------------------------------
// OBTENER TODOS LOS USUARIOS (admin)
// -------------------------------------------------------------
public function obtenerTodos() {
    $sql = "SELECT id, nombre, email, rol, pais FROM usuarios";
    return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// -------------------------------------------------------------
// ACTUALIZAR ROL DE UN USUARIO (admin)
// -------------------------------------------------------------
public function actualizarRol($id, $rol) {
    $sql = "UPDATE usuarios SET rol = ? WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$rol, $id]);
}

}
    