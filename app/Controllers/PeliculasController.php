<?php



class PeliculasController {

    public function cargarPeliculas() {
        $pdo = Database::getConnection();
        $resultado = ConexionPeliculasApi::importarPeliculas($pdo, 20);

        $mensaje = $resultado
            ? "Películas importadas correctamente."
            : "Error al importar películas.";

        include dirname(__DIR__).'/templates/CargarPeliculas.php';
    }

    public function mostrarTop() {
        $pdo = Database::getConnection();
        $peliculas = obtenerTopPeliculas($pdo);
        include dirname(__DIR__).'/templates/perfil.php';
    }
}
