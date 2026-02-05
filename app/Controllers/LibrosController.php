<?php

class LibrosController {

    public function cargarLibros() {
        $pdo = Database::getConnection();
        $resultado = ConexionApiLibros::buscarEImportar($pdo, 20);

        $mensaje = $resultado
            ? "Libros importados correctamente."
            : "Error al importar libros.";

        include dirname(__DIR__).'/templates/CargarLibros.php';
    }

    public function mostrarTop() {
        $modeloLibros = new Libros();
        $libros = $modeloLibros->obtenerTopLibros();

        include dirname(__DIR__).'/templates/perfil.php';
    }
}
