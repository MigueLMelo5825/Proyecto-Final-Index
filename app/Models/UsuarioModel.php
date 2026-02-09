<?php

class UsuarioModel
{

    private $db;

    public function __construct()
    {
$this->db = Database::getConnection();
    }

    // -------------------------------------------------------------
    // REGISTRAR USUARIO
    // -------------------------------------------------------------
    public function registrar($nombre, $email, $hash, $pais)

        { error_log("EJECUTANDO INSERT");

        $sql = "INSERT INTO usuarios (nombre, email, contrasena, rol, nivel, pais, activo)
                VALUES (?, ?, ?, 'usuario', 1, ?, 0)";
        $stmt = $this->db->prepare($sql);

        

        $ok = $stmt->execute([$nombre, $email, $hash, $pais]);

        if ($ok) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // -------------------------------------------------------------
    // VALIDAR LOGIN
    // -------------------------------------------------------------
    public function validarLogin($email, $password)
    {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) return false;
        if (!password_verify($password, $usuario['contrasena'])) return false;

        if (empty($usuario['activo']) || (int)$usuario['activo'] !== 1) {
            return ['__inactivo__' => true];
        }

        return $usuario;
    }

    // -------------------------------------------------------------
    // ELIMINAR USUARIO (admin)
    // -------------------------------------------------------------
    public function eliminar($id)
    {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // -------------------------------------------------------------
    // OBTENER TODOS LOS USUARIOS (admin)
    // -------------------------------------------------------------
    public function obtenerTodos(): array
    {
        $sql = "SELECT id, titulo, autores, categoria, imagen_url FROM libros ORDER BY titulo ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    // -------------------------------------------------------------
    // ACTUALIZAR ROL (admin)
    // -------------------------------------------------------------
    public function actualizarRol($id, $rol)
    {
        $sql = "UPDATE usuarios SET rol = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$rol, $id]);
    }

    // -------------------------------------------------------------
    // BUSCAR POR EMAIL
    // -------------------------------------------------------------
    public function buscarPorEmail($email)
    {
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

    // -------------------------------------------------------------
    // ACTUALIZAR CONTRASEÑA
    // -------------------------------------------------------------
    public function actualizarPassword($usuarioId, $nuevaPassword)
    {
        try {
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

    // -------------------------------------------------------------
    // EMAIL EXISTE
    // -------------------------------------------------------------
    public function emailExiste($email)
    {
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

    // -------------------------------------------------------------
    // OBTENER POR ID
    // -------------------------------------------------------------
    public function obtenerPorId($id)
    {
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

    // -------------------------------------------------------------
    // ACTUALIZAR FOTO DE PERFIL
    // -------------------------------------------------------------
    public function actualizarFoto($idUsuario, $rutaFoto)
    {
        try {
            $sql = "UPDATE usuarios SET foto = :foto WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':foto', $rutaFoto, PDO::PARAM_STR);
            $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar foto: " . $e->getMessage());
            return false;
        }
    }

    // -------------------------------------------------------------
    // ACTUALIZAR BIO
    // -------------------------------------------------------------
    public function actualizarBio($idUsuario, $bio)
    {
        try {
            $sql = "UPDATE usuarios SET bio = :bio WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);
            $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar bio: " . $e->getMessage());
            return false;
        }
    }

    // -------------------------------------------------------------
    // ACTUALIZAR TOP 4 LIBROS
    // -------------------------------------------------------------
    public function actualizarTopLibros($idUsuario, $json)
{
    $sql = "UPDATE usuarios SET top_libros = :json WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':json' => $json,
        ':id' => $idUsuario
    ]);
}


    // -------------------------------------------------------------
    // ACTUALIZAR TOP 4 PELÍCULAS
    // -------------------------------------------------------------
    public function actualizarTopPeliculas($idUsuario, $json)
    {
        try {
            $sql = "UPDATE usuarios SET top_peliculas = :json WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':json', $json, PDO::PARAM_STR);
            $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar top películas: " . $e->getMessage());
            return false;
        }
    }

    // -------------------------------------------------------------
    // TOKENS Y ACTIVACIÓN
    // -------------------------------------------------------------
    public function guardarTokenActivacion(int $idUser, string $token, int $validoHasta): bool
    {
        $sql = "INSERT INTO token_validacion (id_user, token, valido_hasta) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idUser, $token, $validoHasta]);
    }

    public function guardarTokenRecuperacion(int $idUser, string $token, int $validoHasta): bool
    {
        $sql = "INSERT INTO token_recuperacion (id_user, token, valido_hasta) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idUser, $token, $validoHasta]);
    }

    public function buscarTokenRecuperacion(string $token): ?array
    {
        $sql = "SELECT * FROM token_recuperacion WHERE token = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;
        if (time() > (int)$row['valido_hasta']) return null;

        return $row;
    }

    public function borrarTokenRecuperacion(string $token): bool
    {
        $sql = "DELETE FROM token_recuperacion WHERE token = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$token]);
    }

    public function buscarTokenValido(string $token): ?array
    {
        $sql = "SELECT * FROM token_validacion WHERE token = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;
        if (time() > (int)$row['valido_hasta']) return null;

        return $row;
    }

    public function activarUsuario(int $idUser): bool
    {
        $sql = "UPDATE usuarios SET activo = 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idUser]);
    }

    public function borrarToken(string $token): bool
    {
        $sql = "DELETE FROM token_validacion WHERE token = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$token]);
    }
}
