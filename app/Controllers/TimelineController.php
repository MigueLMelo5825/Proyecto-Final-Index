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

        // Cargar top películas
        $pelisModel = new Peliculas();
        $topPeliculas = $pelisModel->obtenerPeliculasAleatorias();

        // Cargar top películas
        //$librosModel = new Libros();
       // $topLibros = $librosModel->obtenerLibrosAleatorios();

       
        
        $eventos = EventoModel::obtenerEventosTimeline($idUsuario);

        require __DIR__ . '/../templates/timeline.php';
    }
}
