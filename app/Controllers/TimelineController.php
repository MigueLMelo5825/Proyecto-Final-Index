<?php


class TimelineController {

    public function index() {

        $model = new TimelineModel();
        $eventos = $model->obtenerActividadReciente();

        require __DIR__ . '/../templates/timeline.php';
    }
}
