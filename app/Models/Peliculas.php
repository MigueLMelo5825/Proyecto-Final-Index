<?php

// ------------------------------------------------------
//  OBTENER NOMBRE DEL GÉNERO (MAPEO DE TMDB → TEXTO)
// ------------------------------------------------------
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


// ------------------------------------------------------
//  OBTENER TOP 5 PELÍCULAS PARA EL PERFIL
// ------------------------------------------------------
function obtenerTopPeliculas(PDO $pdo) {
    $sql = "SELECT id, titulo, genero, portada, descripcion, anio 
            FROM peliculas 
            ORDER BY id DESC 
            LIMIT 5";

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// ------------------------------------------------------
//  GUARDAR PELÍCULAS IMPORTADAS DESDE TMDB
// ------------------------------------------------------
function guardarPeliculas(array $peliculas, PDO $pdo, int $cantidad) {

    $peliculas = array_slice($peliculas, 0, $cantidad);

    $sql = "INSERT INTO peliculas (titulo, anio, portada, descripcion, genero)
            VALUES (:titulo, :anio, :portada, :descripcion, :genero)";

    $stmt = $pdo->prepare($sql);

    foreach ($peliculas as $p) {

        $titulo = $p['title'];
        $anio = substr($p['release_date'], 0, 4);
        $portada = "https://image.tmdb.org/t/p/w300" . $p['poster_path'];
        $descripcion = $p['overview'];
        $genero = $p['genre_ids'][0] ?? null;

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

?>
