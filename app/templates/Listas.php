<?php
require_once dirname(__DIR__) . '/Core/Database.php';

class Listas {

    public static function obtenerListasUsuario($conexion, $idUsuario) {
        $sql = "SELECT id_lista, nombre FROM listas WHERE id_usuario = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
