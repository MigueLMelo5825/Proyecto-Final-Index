<?php

require_once dirname(__DIR__).'/Core/Database.php';
require_once dirname(__DIR__).'/Core/ConexionApiLibros.php';
class LibrosController {

    public function cargarLibros() {
        $pdo = Database::getConnection();
        $resultado = ConexionApiLibros::buscarEImportar($pdo, 20);

        $mensaje = $resultado
            ? "Libros importadas correctamente."
            : "Error al importar libros.";

        include dirname(__DIR__).'/templates/CargarLibros.php';
    }

    public function mostrarTop() {
        $pdo = Database::getConnection();
        $libros = obtenerTopLibros($pdo);
        include dirname(__DIR__).'/templates/perfil.php';
    }
}
