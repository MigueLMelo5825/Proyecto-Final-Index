<?php

<<<<<<< HEAD
//  IMPORTAR LIBRO DESDE GOOGLE BOOKS

function importarLibro($item, PDO $pdo) {
=======
class Libros {
>>>>>>> 0b589a6 (Ajustado perfil y modelos Libros/Peliculas)

    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function obtenerTopLibros(): array {
        $sql = "SELECT titulo, autores, categoria, imagen_url, preview_link 
                FROM libros 
                ORDER BY fecha_importacion DESC 
                LIMIT 5";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerLibros(): array {
        $sql = "SELECT titulo, autores, categoria, imagen_url, preview_link 
                FROM libros 
                ORDER BY titulo ASC 
                LIMIT 20";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function importarLibro(array $item): bool {
        $info = $item['volumeInfo'] ?? null;
        if (!$info) return false;

        $autores = isset($info['authors']) ? implode(", ", $info['authors']) : 'Autor Desconocido';

        $isbn10 = null;
        $isbn13 = null;

        if (isset($info['industryIdentifiers'])) {
            foreach ($info['industryIdentifiers'] as $id) {
                if ($id['type'] === 'ISBN_10') $isbn10 = $id['identifier'];
                if ($id['type'] === 'ISBN_13') $isbn13 = $id['identifier'];
            }
        }

        $descripcion = isset($info['description']) ? strip_tags($info['description']) : null;

        $sql = "INSERT IGNORE INTO libros (
                    id, titulo, subtitulo, autores, editorial, 
                    fecha_publicacion, descripcion, isbn_10, isbn_13, 
                    paginas, categoria, imagen_url, idioma, preview_link
                ) VALUES (
                    :id, :titulo, :subtitulo, :autores, :editorial, 
                    :fecha, :desc, :isbn10, :isbn13, 
                    :paginas, :categoria, :imagen, :idioma, :link
                )";

        try {
            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([
                ':id'         => $item['id'],
                ':titulo'     => $info['title'] ?? 'Sin título',
                ':subtitulo'  => $info['subtitle'] ?? null,
                ':autores'    => $autores,
                ':editorial'  => $info['publisher'] ?? null,
                ':fecha'      => $info['publishedDate'] ?? null,
                ':desc'       => $descripcion,
                ':isbn10'     => $isbn10,
                ':isbn13'     => $isbn13,
                ':paginas'    => $info['pageCount'] ?? 0,
                ':categoria'  => isset($info['categories']) ? $info['categories'][0] : null,
                ':imagen'     => $info['imageLinks']['thumbnail'] ?? null,
                ':idioma'     => $info['language'] ?? null,
                ':link'       => $info['previewLink'] ?? null
            ]);

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
<<<<<<< HEAD

//  OBTENER LISTA GENERAL DE LIBROS

function obtenerLibros(PDO $pdo) {
    $sql = "SELECT titulo, autores, categoria, imagen_url, preview_link 
            FROM libros 
            ORDER BY titulo ASC 
            LIMIT 20";

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


//  OBTENER TOP 5 LIBROS PARA EL PERFIL

function obtenerTopLibros(PDO $pdo) {

    // Tu tabla SÍ tiene fecha_importacion → perfecto
    $sql = "SELECT titulo, autores, categoria, imagen_url, preview_link 
            FROM libros 
            ORDER BY fecha_importacion DESC 
            LIMIT 5";

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerLibroPorId(PDO $conexionBD, string $idLibro): ?array {

    $consulta = "
        SELECT 
            id,
            titulo,
            subtitulo,
            autores,
            editorial,
            fecha_publicacion,
            descripcion,
            isbn_10,
            isbn_13,
            paginas,
            categoria,
            imagen_url,
            idioma,
            preview_link
        FROM libros
        WHERE id = :idLibro
        LIMIT 1
    ";

    $sentencia = $conexionBD->prepare($consulta);
    $sentencia->execute([
        ':idLibro' => $idLibro
    ]);

    $libro = $sentencia->fetch(PDO::FETCH_ASSOC);

    return $libro ?: null;
}



?>
=======
>>>>>>> 0b589a6 (Ajustado perfil y modelos Libros/Peliculas)
