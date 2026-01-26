<?php
function obtenerNombreGenero($id) {
    $generos = [
        28 => "Acción",
        12 => "Aventura",
        16 => "Animación",
        35 => "Comedia",
        80 => "Crimen",
        99 => "Documental",
        18 => "Drama",
        10751 => "Familia",
        14 => "Fantasía",
        36 => "Historia",
        27 => "Terror",
        10402 => "Música",
        9648 => "Misterio",
        10749 => "Romance",
        878 => "Ciencia ficción",
        10770 => "TV Movie",
        53 => "Suspense",
        10752 => "Bélica",
        37 => "Western"
    ];

    return $generos[$id] ?? "Desconocido";
}


// Obtener top 5 películas para el perfil
function obtenerTopPeliculas($pdo) {
    $sql = "SELECT id, titulo, genero, portada, descripcion, anio 
            FROM peliculas 
            ORDER BY id DESC 
            LIMIT 5";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Importar películas desde TMDB
function importarPeliculasTMDB($pdo, $cantidad = 20) {

    // TU API KEY v3
    $apiKey = "1af9c7bfbe2d47b30483f4c7ab743391";

    // Endpoint de TMDB
    $url = "https://api.themoviedb.org/3/movie/popular?api_key={$apiKey}&language=es-ES&page=1";

    $json = file_get_contents($url);
    $data = json_decode($json, true);

    if (!isset($data['results'])) {
        return false;
    }

    $peliculas = array_slice($data['results'], 0, $cantidad);

    $sql = "INSERT INTO peliculas (titulo, anio, portada, descripcion, genero)
            VALUES (:titulo, :anio, :portada, :descripcion, :genero)";

    $stmt = $pdo->prepare($sql);

    foreach ($peliculas as $p) {

        $titulo = $p['title'];
        $anio = substr($p['release_date'], 0, 4);
        $portada = "https://image.tmdb.org/t/p/w300" . $p['poster_path'];
        $descripcion = $p['overview'];

        // Guardamos solo el primer género
        $genero = "";
        if (!empty($p['genre_ids'])) {
            $genero = $p['genre_ids'][0];
        }

        $stmt->execute([
            ':titulo' => $titulo,
            ':anio' => $anio,
            ':portada' => $portada,
            ':descripcion' => $descripcion,
            ':genero' => $genero
        ]);
    }

    return true;
}
