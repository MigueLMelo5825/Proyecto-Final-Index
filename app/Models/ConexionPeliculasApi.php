<?php

class ConexionPeliculasApi {

    private static string $apiKey = "1af9c7bfbe2d47b30483f4c7ab743391";

    // public static function importarPeliculas(PDO $pdo, int $cantidad = 20): bool {

    //     $url = "https://api.themoviedb.org/3/movie/popular?api_key=" . self::$apiKey . "&language=es-ES&page=1";

    //     $json = @file_get_contents($url);
    //     if ($json === false) {
    //         return false;
    //     }

    //     $data = json_decode($json, true);
    //     if (!isset($data['results'])) {
    //         return false;
    //     }

     
    //     $peliculasModel = new Peliculas();
    //     return $peliculasModel->guardarPeliculas($data['results'], $cantidad);
    // }






public static function importarPeliculas(PDO $pdo, int $cantidad = 200, string $endpoint = "popular"): bool {

            $language = "es-ES";
            $peliculasModel = new Peliculas($pdo); // si tu modelo usa PDO, pásaselo aquí
            $guardadas = 0;
            $page = 1;

            while ($guardadas < $cantidad) {
                $url = "https://api.themoviedb.org/3/movie/$endpoint"
                    . "?api_key=" . self::$apiKey
                    . "&language=$language"
                    . "&page=$page";

                $json = @file_get_contents($url);
                if ($json === false) return false;

                $datos = json_decode($json, true);
                if (!isset($datos["results"]) || empty($datos["results"])) break;

                $restantes = $cantidad - $guardadas;

                
                $ok = $peliculasModel->guardarPeliculas($datos["results"], $restantes);
                if (!$ok) return false;

                //  actualiza guardadas
                $guardadas += min(count($datos["results"]), $restantes);

                // siguiente página
                $page++;

                // cortar si no hay más páginas
                if (isset($datos["total_pages"]) && $page > (int)$datos["total_pages"]) break;
                if ($page > 500) break; // límite típico de TMDB
            }

            return true;




}
}