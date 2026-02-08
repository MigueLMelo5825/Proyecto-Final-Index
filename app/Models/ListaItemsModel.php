<?php
class ListaItemsModel {

    public static function obtenerItems($conexion, $idLista) {
        $sql = "SELECT titulo, descripcion, añadido_en
                FROM listas_items
                WHERE id_lista = ?
                ORDER BY añadido_en DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([$idLista]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
