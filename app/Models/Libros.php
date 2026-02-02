<?php

class Libros {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    // Obtener top 5 libros
    public function obtenerTopLibros(): array {
        $sql = "SELECT id, titulo, autores, categoria, imagen_url 
                FROM libros 
                ORDER BY id DESC 
                LIMIT 5";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Guardar libros importados desde API
    public function guardarLibros(array $libros, int $cantidad): bool {

        $libros = array_slice($libros, 0, $cantidad);

        $sql = "INSERT INTO libros (titulo, autores, categoria, imagen_url)
                VALUES (:titulo, :autores, :categoria, :imagen_url)";

        $stmt = $this->pdo->prepare($sql);

        foreach ($libros as $l) {

            $titulo = $l['volumeInfo']['title'] ?? 'Sin título';
            $autores = isset($l['volumeInfo']['authors'])
                ? implode(', ', $l['volumeInfo']['authors'])
                : 'Autor desconocido';

            $categoria = $l['volumeInfo']['categories'][0] ?? 'Sin categoría';

            $imagen = $l['volumeInfo']['imageLinks']['thumbnail']
                ?? 'web/img/fallback.png';

            $stmt->execute([
                ':titulo' => $titulo,
                ':autores' => $autores,
                ':categoria' => $categoria,
                ':imagen_url' => $imagen
            ]);
        }

        return true;
    }
}
