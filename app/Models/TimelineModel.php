<?php

class TimelineModel {

    private $db;

    public function __construct() {
        
        $this->db = Database::getConnection();
    }

    public function obtenerActividadReciente($limite = 20) {
        $sql = "SELECT * FROM actividad ORDER BY fecha DESC LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
