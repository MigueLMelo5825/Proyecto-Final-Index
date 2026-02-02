<?php

class UsuarioController {

    public function perfil() {


        $librosModel = new Libros();

        $pelisModel  = new Peliculas();

        $topLibros = $librosModel->obtenerTopLibros();

        $topPeliculas = $pelisModel->obtenerTopPeliculas();

        foreach ($topPeliculas as &$p) {
            $p['genero_nombre'] = $pelisModel->obtenerNombreGenero($p['genero']);
        }

        require __DIR__ . '/../templates/perfil.php';
    }
}
