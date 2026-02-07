<?php


require_once __DIR__ . '/../Core/Database.php';

class TokenRecuperacion {
    
    private $db;
    private $table = 'tokens_recuperacion';
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    /**
     * Crea un nuevo token de recuperación
     * 
     * @param int $usuarioId ID del usuario
     * @param string $token Token generado
     * @param int $horasValidez Horas de validez del token (por defecto 1 hora)
     * @return bool True si se creó correctamente
     */
    public function crear($usuarioId, $token, $horasValidez = 1) {
        try {
            // Limpiar tokens antiguos del usuario
            $this->limpiarTokensUsuario($usuarioId);
            
            // Calcular fecha de expiración
            $fechaExpiracion = date('Y-m-d H:i:s', strtotime("+$horasValidez hour"));
            
            // Insertar nuevo token
            $query = "INSERT INTO " . $this->table . " 
                      (usuario_id, token, fecha_expiracion) 
                      VALUES (:usuario_id, :token, :fecha_expiracion)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_expiracion', $fechaExpiracion, PDO::PARAM_STR);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Error al crear token de recuperación: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Valida un token de recuperación
     * 
     * @param string $token Token a validar
     * @return array|false Datos del token si es válido, false si no
     */
    public function validar($token) {
        try {
            $query = "SELECT * FROM " . $this->table . " 
                      WHERE token = :token 
                      AND usado = 0 
                      AND fecha_expiracion > NOW()
                      LIMIT 1";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado ? $resultado : false;
            
        } catch (PDOException $e) {
            error_log("Error al validar token: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Marca un token como usado
     * 
     * @param string $token Token a marcar
     * @return bool True si se marcó correctamente
     */
    public function marcarComoUsado($token) {
        try {
            $query = "UPDATE " . $this->table . " 
                      SET usado = 1 
                      WHERE token = :token";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Error al marcar token como usado: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Limpia tokens antiguos o usados de un usuario
     * 
     * @param int $usuarioId ID del usuario
     * @return bool True si se limpiaron correctamente
     */
    private function limpiarTokensUsuario($usuarioId) {
        try {
            $query = "DELETE FROM " . $this->table . " 
                      WHERE usuario_id = :usuario_id 
                      AND (usado = 1 OR fecha_expiracion < NOW())";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Error al limpiar tokens del usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Limpia todos los tokens expirados de la base de datos
     * Este método puede ser llamado periódicamente con un cron
     * 
     * @return bool True si se limpiaron correctamente
     */
    public function limpiarExpirados() {
        try {
            $query = "DELETE FROM " . $this->table . " 
                      WHERE fecha_expiracion < NOW() OR usado = 1";
            
            $stmt = $this->db->prepare($query);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Error al limpiar tokens expirados: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene un token por su valor
     * 
     * @param string $token Token a buscar
     * @return array|false Datos del token o false
     */
    public function obtenerPorToken($token) {
        try {
            $query = "SELECT * FROM " . $this->table . " 
                      WHERE token = :token 
                      LIMIT 1";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener token: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica si un usuario tiene tokens activos
     * 
     * @param int $usuarioId ID del usuario
     * @return int Número de tokens activos
     */
    public function contarTokensActivos($usuarioId) {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                      WHERE usuario_id = :usuario_id 
                      AND usado = 0 
                      AND fecha_expiracion > NOW()";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado ? (int)$resultado['total'] : 0;
            
        } catch (PDOException $e) {
            error_log("Error al contar tokens activos: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Elimina todos los tokens de un usuario

     * 
     * @param int $usuarioId ID del usuario
     * @return bool True si se eliminaron correctamente
     */
    public function eliminarTodosDelUsuario($usuarioId) {
        try {
            $query = "DELETE FROM " . $this->table . " 
                      WHERE usuario_id = :usuario_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Error al eliminar tokens del usuario: " . $e->getMessage());
            return false;
        }
    }
}
