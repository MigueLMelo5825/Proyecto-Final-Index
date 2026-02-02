<?php

require_once __DIR__ . '/../Models/TimelineModel.php';

class TimelineController {

    public function index() {

        $model = new TimelineModel();
        $eventos = $model->obtenerActividadReciente();

        require __DIR__ . '/../templates/timeline.php';
    }
}
