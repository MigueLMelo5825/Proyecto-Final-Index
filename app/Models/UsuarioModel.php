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

//-------------------------------------------------------------

public function buscarPorEmail($email) {
    try {
        $query = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error al buscar usuario por email: " . $e->getMessage());
        return false;
    }
}


public function actualizarPassword($usuarioId, $nuevaPassword) {
    try {
        // Hashear la contraseña
        $passwordHash = password_hash($nuevaPassword, PASSWORD_BCRYPT);
        
        $query = "UPDATE usuarios SET contrasena = :contrasena WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':contrasena', $passwordHash, PDO::PARAM_STR);
        $stmt->bindParam(':id', $usuarioId, PDO::PARAM_INT);
        
        return $stmt->execute();
        
    } catch (PDOException $e) {
        error_log("Error al actualizar contraseña: " . $e->getMessage());
        return false;
    }
}


public function emailExiste($email) {
    try {
        $query = "SELECT COUNT(*) as total FROM usuarios WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado && $resultado['total'] > 0;
        
    } catch (PDOException $e) {
        error_log("Error al verificar email: " . $e->getMessage());
        return false;
    }
}


public function obtenerPorId($id) {
    try {
        $query = "SELECT * FROM usuarios WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error al obtener usuario por ID: " . $e->getMessage());
        return false;
    }
}


}
    