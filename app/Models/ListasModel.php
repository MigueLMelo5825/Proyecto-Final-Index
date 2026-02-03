<?php
class ListaModel {

    private $db;

    public function __construct() {
        $this->db = Conexion::getConexion();
    }

    public function crearLista($idUsuario, $nombre, $descripcion, $tipo) {
        $sql = "INSERT INTO listas (id_usuario, nombre, descripcion, tipo, creada_en)
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idUsuario, $nombre, $descripcion, $tipo]);
    }

    public static function obtenerListasUsuario($idUsuario) {
        $sql = "SELECT * FROM listas WHERE id_usuario = ?";
        $db = Conexion::getConexion();
        $stmt = $db->prepare($sql);
        $stmt->execute([$idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function añadirItem($idLista, $idLibro, $idPelicula) {
        $sql = "INSERT INTO listas_items (id_lista, id_libro, id_pelicula, añadido_en)
                VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idLista, $idLibro, $idPelicula]);
    }

    public function obtenerItemsLista($idLista) {
        $sql = "SELECT * FROM listas_items WHERE id_lista = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idLista]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
