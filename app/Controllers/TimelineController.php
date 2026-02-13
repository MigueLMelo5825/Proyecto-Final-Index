<?php

class TimelineController
{
    private $session;

    public function __construct($session)
    {
        $this->session = $session;
    }

    public function mostrar()
    {
        $this->session->checkSecurity();
        $idUsuario = $this->session->get('id_usuario');

        $librosModel = new Libros();
        $pelisModel  = new Peliculas();

        // 4 libros aleatorios
        $topLibros = $librosModel->obtenerLibrosAleatorios(4);

        // 4 pelis aleatorias (esto ya lo tenías parecido)
        $topPeliculas = $pelisModel->obtenerPeliculasAleatorias(4);

        // Eventos del timeline
        $eventos = EventoModel::obtenerEventosTimeline($idUsuario);

        require __DIR__ . '/../templates/timeline.php';
    }
}
