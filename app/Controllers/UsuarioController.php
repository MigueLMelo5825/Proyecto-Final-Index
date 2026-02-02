<?php

class UsuarioController {

    public function perfil() {

        $librosModel = new Libros();
        $pelisModel  = new Peliculas();

        // Obtener datos
        $topLibros = $librosModel->obtenerTopLibros();
        $topPeliculas = $pelisModel->obtenerTopPeliculas();

        // Traducir género ANTES de enviar a la vista
        foreach ($topPeliculas as &$p) {
            $p['genero_nombre'] = $pelisModel->obtenerNombreGenero($p['genero']);
        }

        // Datos del usuario (provisional)
        $usuario = [
            'nombre' => 'Usuario invitado',
            'bio'    => '',
            'foto'   => 'web/img/default.jpg'
        ];

        // Cargar vista
        require __DIR__ . '/../templates/perfil.php';
    }
}
