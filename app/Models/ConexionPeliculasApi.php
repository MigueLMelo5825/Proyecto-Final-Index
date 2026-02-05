<?php

class ConexionPeliculasApi {

    private static string $apiKey = "1af9c7bfbe2d47b30483f4c7ab743391";

    public static function importarPeliculas(PDO $pdo, int $cantidad = 20): bool {

        $url = "https://api.themoviedb.org/3/movie/popular?api_key=" . self::$apiKey . "&language=es-ES&page=1";

        $json = @file_get_contents($url);
        if ($json === false) {
            return false;
        }

        $data = json_decode($json, true);
        if (!isset($data['results'])) {
            return false;
        }

        // ✔ Ahora usamos el modelo Peliculas
        $peliculasModel = new Peliculas();
        return $peliculasModel->guardarPeliculas($data['results'], $cantidad);
    }
}
