<?php

class Peliculas {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    // ------------------------------------------------------
    //  OBTENER NOMBRE DEL GÉNERO (MAPEO DE TMDB → TEXTO)
    // ------------------------------------------------------
    public static function obtenerNombreGenero($id): string {
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

    public function obtenerTodas(): array {
    $sql = "SELECT id, titulo, genero, portada, descripcion, anio 
            FROM peliculas 
            ORDER BY titulo ASC";

    $stmt = $this->pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // ------------------------------------------------------
    //  OBTENER TOP 5 PELÍCULAS PARA EL PERFIL
    // ------------------------------------------------------
    public function obtenerTopPeliculas(): array {
        $sql = "SELECT id, titulo, genero, portada, descripcion, anio 
                FROM peliculas 
                ORDER BY id DESC 
                LIMIT 4";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorIds(array $ids): array {
    if (empty($ids)) return [];

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "SELECT * FROM peliculas WHERE id IN ($placeholders)";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($ids);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // ------------------------------------------------------
    //  GUARDAR PELÍCULAS IMPORTADAS DESDE TMDB
    // ------------------------------------------------------
    public function guardarPeliculas(array $peliculas, int $cantidad): bool {

        $peliculas = array_slice($peliculas, 0, $cantidad);

        $sql = "INSERT INTO peliculas (titulo, anio, portada, descripcion, genero)
                VALUES (:titulo, :anio, :portada, :descripcion, :genero)";

        $stmt = $this->pdo->prepare($sql);

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
}
