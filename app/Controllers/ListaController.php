<?php
require_once dirname(__DIR__) . '/Models/Listas.php';

class ListaController {

    public function añadir() {
        session_start();

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: index.php?ctl=inicio");
            exit;
        }

        $idLista = $_POST['id_lista'] ?? null;
        $idLibro = $_POST['id_libro'] ?? null;
        $idPelicula = $_POST['id_pelicula'] ?? null;

        if (!$idLista || (!$idLibro && !$idPelicula)) {
            header("Location: index.php?ctl=perfil");
            exit;
        }

        $conexion = Database::getConnection();

        $sql = "INSERT INTO listas_items (id_lista, id_libro, id_pelicula, añadido_en)
                VALUES (?, ?, ?, NOW())";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$idLista, $idLibro, $idPelicula]);

        header("Location: index.php?ctl=perfil");
        exit;
    }
}
