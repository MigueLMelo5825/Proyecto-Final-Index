<?php

// require_once __DIR__ . '/../core/Database.php';
// require_once __DIR__ . '/../Models/Libros.php';
// require_once __DIR__ . '/../Models/Peliculas.php';

class UsuarioController {

    public function perfil() {

        $pdo = Database::getConnection();

        $topLibros = obtenerTopLibros($pdo);
        $topPeliculas = obtenerTopPeliculas($pdo);

        $usuario = [
            'nombre' => 'Isabel Paredes',
            'bio' => 'De Alfara del Patriarca',
            'foto' => '/Proyecto/web/img/default.jpg'
        ];

        include __DIR__ . '/../templates/perfil.php';
    }

    public function timeline() {
        include __DIR__ . '/../templates/timeline.php';
    }
}
