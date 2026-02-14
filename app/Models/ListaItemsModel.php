<?php
class ListaItemsModel {

    public static function obtenerItems($conexion, $idLista) {

        $sql = "SELECT 
                    COALESCE(p.titulo, l.titulo) AS titulo,
                    COALESCE(p.descripcion, l.descripcion) AS descripcion,
                    COALESCE(p.portada, l.imagen_url) AS imagen,
                    li.añadido_en
                FROM listas_items li
                LEFT JOIN peliculas p ON li.id_pelicula = p.id
                LEFT JOIN libros l ON li.id_libro = l.id
                WHERE li.id_lista = ?
                ORDER BY li.añadido_en DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([$idLista]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*public static function obtenerItems($conexion, $idLista) {
        $sql = "SELECT titulo, descripcion, añadido_en
                FROM listas_items 
                WHERE id_lista = ?
                ORDER BY añadido_en DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([$idLista]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }*/
}
