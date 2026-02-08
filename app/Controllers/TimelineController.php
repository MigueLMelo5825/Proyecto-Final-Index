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

        $conexion = Database::getConnection();

        // Traer eventos recientes
        $sql = "SELECT tipo, titulo, descripcion, DATE_FORMAT(fecha, '%d/%m/%Y %H:%i') as fecha
                FROM eventos
                WHERE id_usuario = ?
                ORDER BY fecha DESC
                LIMIT 50";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$idUsuario]);
        $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../templates/timeline.php';
    }
}
