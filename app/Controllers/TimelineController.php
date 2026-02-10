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

        // Usar el método correcto del modelo
        $eventos = EventoModel::obtenerEventosTimeline($idUsuario);

        require __DIR__ . '/../templates/timeline.php';
    }
}
