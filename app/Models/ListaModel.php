<?php
require_once dirname(__DIR__) . '/Core/Database.php';

class ListaModel {

    /**
     * Obtener todas las listas de un usuario
     */
public function obtenerListasPorUsuario(int $idUsuario): array
{
    $conexion = Database::getConnection();

    $sql = "SELECT id, nombre, descripcion, tipo, creada_en
            FROM listas
            WHERE id_usuario = ?
            ORDER BY creada_en DESC";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$idUsuario]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    /**
     * Crear una nueva lista
     */
    public function crearLista(int $idUsuario, string $nombre, string $descripcion = null, string $tipo = 'personal'): bool {
        $conexion = Database::getConnection();

        $sql = "INSERT INTO listas (id_usuario, nombre, descripcion, tipo, creada_en)
                VALUES (?, ?, ?, ?, NOW())";

        $stmt = $conexion->prepare($sql);
        return $stmt->execute([$idUsuario, $nombre, $descripcion, $tipo]);
    }
}
