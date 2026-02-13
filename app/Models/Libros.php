<?php

use function PHPSTORM_META\type;

class Libros {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    
public function obtenerLibrosAleatorios(int $limite = 4): array
{
    $sql = "SELECT * FROM libros ORDER BY RAND() LIMIT :limite";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



    // Obtener top 5 libros
    public function obtenerTopLibros(): array {
        $sql = "SELECT id, titulo, autores, categoria, imagen_url 
                FROM libros 
                ORDER BY id DESC 
                LIMIT 4";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   // Guardar libros importados desde API

public function guardarLibros(array $libros, int $cantidad): bool {

    $libros = array_slice($libros, 0, $cantidad);

    $sql = "INSERT INTO libros 
            (id, titulo, subtitulo, autores, editorial, fecha_publicacion, descripcion, 
             isbn_10, isbn_13, paginas, categoria, imagen_url, idioma, preview_link, fecha_importacion)
            VALUES 
            (:id, :titulo, :subtitulo, :autores, :editorial, :fecha_publicacion, :descripcion,
             :isbn_10, :isbn_13, :paginas, :categoria, :imagen_url, :idioma, :preview_link, NOW())
            ON DUPLICATE KEY UPDATE 
                titulo = VALUES(titulo),
                subtitulo = VALUES(subtitulo),
                autores = VALUES(autores),
                editorial = VALUES(editorial),
                fecha_publicacion = VALUES(fecha_publicacion),
                descripcion = VALUES(descripcion),
                isbn_10 = VALUES(isbn_10),
                isbn_13 = VALUES(isbn_13),
                paginas = VALUES(paginas),
                categoria = VALUES(categoria),
                imagen_url = VALUES(imagen_url),
                idioma = VALUES(idioma),
                preview_link = VALUES(preview_link)";

    $stmt = $this->pdo->prepare($sql);

    foreach ($libros as $l) {

        $info = $l['volumeInfo'] ?? [];

        // ID
        $id = $l['id'] ?? null;
        if (!$id) continue;

        // FILTROS DE CALIDAD
        if (!isset($info['imageLinks']['thumbnail'])) continue;
        if (!isset($info['description'])) continue;
        if (!isset($info['publishedDate'])) continue;

        // Año mínimo 2000
        $year = intval(substr($info['publishedDate'], 0, 4));
        if ($year < 2000) continue;

        // Autores
        if (!isset($info['authors'])) continue;

        // Categoría
        if (!isset($info['categories'])) continue;

        $categoria = $info['categories'][0];

        // FILTRO POR CATEGORÍAS PERMITIDAS (solo novelas)
        $categoriasPermitidas = [
            'Fiction',
            'Juvenile Fiction',
            'Young Adult Fiction',
            'Romance',
            'Fantasy',
            'Science Fiction',
            'Horror',
            'Thriller',
            'Mystery',
            'Adventure',
            'Comics & Graphic Novels'
        ];

        if (!in_array($categoria, $categoriasPermitidas)) {
            continue;
        }

        // DATOS
        $titulo = $info['title'] ?? 'Sin título';
        $subtitulo = $info['subtitle'] ?? null;
        $autores = implode(', ', $info['authors']);
        $editorial = $info['publisher'] ?? null;
        $fecha_publicacion = $info['publishedDate'] ?? null;
        $descripcion = $info['description'] ?? null;

        // ISBN
        $isbn_10 = null;
        $isbn_13 = null;

        if (isset($info['industryIdentifiers'])) {
            foreach ($info['industryIdentifiers'] as $idn) {
                if ($idn['type'] === 'ISBN_10') $isbn_10 = $idn['identifier'];
                if ($idn['type'] === 'ISBN_13') $isbn_13 = $idn['identifier'];
            }
        }

        $paginas = $info['pageCount'] ?? null;
        $imagen_url = $info['imageLinks']['thumbnail'];
        $idioma = $info['language'] ?? null;
        $preview_link = $info['previewLink'] ?? null;

        // INSERTAR
        $stmt->execute([
            ':id' => $id,
            ':titulo' => $titulo,
            ':subtitulo' => $subtitulo,
            ':autores' => $autores,
            ':editorial' => $editorial,
            ':fecha_publicacion' => $fecha_publicacion,
            ':descripcion' => $descripcion,
            ':isbn_10' => $isbn_10,
            ':isbn_13' => $isbn_13,
            ':paginas' => $paginas,
            ':categoria' => $categoria,
            ':imagen_url' => $imagen_url,
            ':idioma' => $idioma,
            ':preview_link' => $preview_link
        ]);
    }

    return true;
}
public function obtenerPorIds(array $ids): array
{
    if (empty($ids)) {
        return [];
    }

    $ids = array_filter($ids, fn($id) => is_string($id) && $id !== '');

    if (empty($ids)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "SELECT * FROM libros WHERE id IN ($placeholders)";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($ids);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function obtenerTodos(): array
{
    $sql = "SELECT * FROM libros ORDER BY titulo ASC";
    $stmt = $this->pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



    public static function obtenerLibroPelicula(PDO $conexionBD, string $id, string $type): ?array {
        
        $tabla = ($type === "libro") ? "libros" : "peliculas";
    
        try {
            
            if ($tabla === "libros") {

                $consultaSql = "SELECT id, titulo, subtitulo, autores, editorial, fecha_publicacion, descripcion, isbn_10, isbn_13, paginas, categoria, imagen_url, idioma, preview_link
                FROM libros
                WHERE id = ?";

            } else {
                //buscamos en la tabla peliculas
                $consultaSql = "SELECT id, titulo, anio, portada, descripcion, genero
                FROM peliculas 
                WHERE id = ?";
            }

            $sentencia = $conexionBD->prepare($consultaSql);
            $sentencia->execute([$id]);
            $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);

            if (!$resultado) {
                throw new Exception("No se encontró el registro en la tabla $tabla.");
            }

            // IMPORTANTE: retornamos el objeto encontrado
            return $resultado;

        } catch (Exception $e) {
            error_log("Error en obtenerLibroPelicula: " . $e->getMessage());
            return null; // Retorna null si algo falla
        }
    }
    
}
