<?php

class EventoModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // ============================================================
    // REGISTRAR EVENTO
    // ============================================================
    public function registrarEvento(int $idUsuario, string $tipo, string $titulo, string $descripcion = null): bool
    {
        try {
            $sql = "INSERT INTO eventos (id_usuario, tipo, titulo, descripcion, fecha)
                    VALUES (?, ?, ?, ?, NOW())";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$idUsuario, $tipo, $titulo, $descripcion]);

        } catch (PDOException $e) {
            error_log("Error al registrar evento: " . $e->getMessage());
            return false;
        }
    }

    // ============================================================
    // OBTENER TODOS LOS EVENTOS (TIMELINE GENERAL)
    // ============================================================
    public function obtenerEventos(int $limite = 50): array
    {
        try {
            $sql = "SELECT e.*, u.nombre 
                    FROM eventos e
                    JOIN usuarios u ON u.id = e.id_usuario
                    ORDER BY e.fecha DESC
                    LIMIT ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $limite, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener eventos: " . $e->getMessage());
            return [];
        }
    }

    // ============================================================
    // OBTENER EVENTOS DE UN USUARIO
    // ============================================================
    public function obtenerEventosUsuario(int $idUsuario, int $limite = 50): array
    {
        try {
            $sql = "SELECT e.*, u.nombre
                    FROM eventos e
                    JOIN usuarios u ON u.id = e.id_usuario
                    WHERE e.id_usuario = ?
                    ORDER BY e.fecha DESC
                    LIMIT ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $idUsuario, PDO::PARAM_INT);
            $stmt->bindValue(2, $limite, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener eventos del usuario: " . $e->getMessage());
            return [];
        }
    }
}
