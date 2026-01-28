<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../Models/Libros.php';
require_once __DIR__ . '/../Models/Peliculas.php';

class UsuarioController {

    public function perfil() {

        // 1. Conexión a la base de datos
        $pdo = Database::getConnection();

        // 2. Obtener datos reales desde los modelos
        $topLibros = obtenerTopLibros($pdo);
        $topPeliculas = obtenerTopPeliculas($pdo);

        // 3. Datos del usuario (de momento estático)
        $usuario = [
            'nombre' => 'Isabel Paredes',
            'bio' => 'De Alfara del Patriarca',
            'foto' => '/INDEX_proYECTO/web/img/default.jpg'
        ];

        // 4. Cargar la vista
        var_dump($topLibros);
var_dump($topPeliculas);

        include __DIR__ . '/../templates/perfil.php';
    }

    public function timeline() {
        include __DIR__ . '/../templates/timeline.php';
    }
}

