<?php

// require_once __DIR__ . '/../core/Database.php';
// require_once __DIR__ . '/../Models/Libros.php';
// require_once __DIR__ . '/../Models/Peliculas.php';

class UsuarioController {

    public function perfil() {

        // Modelos
        $librosModel = new Libros();
        $pelisModel  = new Peliculas();

        // Datos
        $topLibros = $librosModel->obtenerTopLibros();
        $topPeliculas = $pelisModel->obtenerTopPeliculas();

        // Usuario de prueba
        $usuario = [
            'nombre' => 'Isabel Paredes',
            'bio' => 'De Alfara del Patriarca',
            'foto' => '/Proyecto/web/img/default.jpg'
        ];

        // Vista
        include __DIR__ . '/../templates/perfil.php';
    }

    public function timeline() {
        include __DIR__ . '/../templates/timeline.php';
    }
}
