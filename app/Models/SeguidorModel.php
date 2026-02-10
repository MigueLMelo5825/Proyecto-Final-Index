<?php

class SeguidorModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // -------------------------------------------------------------
    // SEGUIR A UN USUARIO
    // -------------------------------------------------------------
    public function seguir(int $seguidorId, int $seguidoId): bool
    {
        try {
            $sql = "INSERT IGNORE INTO seguidores (seguidor_id, seguido_id)
                    VALUES (:seguidor, :seguido)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':seguidor' => $seguidorId,
                ':seguido'  => $seguidoId
            ]);
        } catch (PDOException $e) {
            error_log("Error al seguir usuario: " . $e->getMessage());
            return false;
        }
    }

    // -------------------------------------------------------------
    // DEJAR DE SEGUIR
    // -------------------------------------------------------------
    public function dejarDeSeguir(int $seguidorId, int $seguidoId): bool
    {
        try {
            $sql = "DELETE FROM seguidores 
                    WHERE seguidor_id = :seguidor AND seguido_id = :seguido";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':seguidor' => $seguidorId,
                ':seguido'  => $seguidoId
            ]);
        } catch (PDOException $e) {
            error_log("Error al dejar de seguir: " . $e->getMessage());
            return false;
        }
    }

    // -------------------------------------------------------------
    // ¿YA LO SIGUE?
    // -------------------------------------------------------------
    public function esSeguidor(int $seguidorId, int $seguidoId): bool
    {
        $sql = "SELECT COUNT(*) FROM seguidores 
                WHERE seguidor_id = :seguidor AND seguido_id = :seguido";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':seguidor' => $seguidorId,
            ':seguido'  => $seguidoId
        ]);

        return $stmt->fetchColumn() > 0;
    }

    // -------------------------------------------------------------
    // LISTA DE SEGUIDORES
    // -------------------------------------------------------------
    public function obtenerSeguidores(int $usuarioId): array
    {
        $sql = "SELECT u.id, u.nombre, u.foto 
                FROM seguidores s
                JOIN usuarios u ON u.id = s.seguidor_id
                WHERE s.seguido_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $usuarioId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------------
    // LISTA DE SEGUIDOS
    // -------------------------------------------------------------
    public function obtenerSeguidos(int $usuarioId): array
    {
        $sql = "SELECT u.id, u.nombre, u.foto 
                FROM seguidores s
                JOIN usuarios u ON u.id = s.seguido_id
                WHERE s.seguidor_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $usuarioId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
