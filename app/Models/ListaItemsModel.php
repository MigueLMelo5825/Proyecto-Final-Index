<?php
require_once dirname(__DIR__) . '/Core/Database.php';

class ListaItemsModel {

    public static function obtenerItems($conexion, int $idLista): array {
        $sql = "SELECT id, titulo, descripcion, añadido_en AS creado_en
                FROM listas_items
                WHERE id_lista = ?
                ORDER BY añadido_en DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([$idLista]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function agregarItem($conexion, int $idLista, string $titulo, string $descripcion = null): bool {
        $sql = "INSERT INTO listas_items (id_lista, titulo, descripcion, añadido_en)
                VALUES (?, ?, ?, NOW())";

        $stmt = $conexion->prepare($sql);
        return $stmt->execute([$idLista, $titulo, $descripcion]);
    }
}
