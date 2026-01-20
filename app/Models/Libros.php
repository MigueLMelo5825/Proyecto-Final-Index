<?php

function importarLibro($item, $pdo) {
    // 1. Extraemos la información básica del nodo volumeInfo
    $info = $item['volumeInfo'] ?? null;
    if (!$info) return false;

    // 2. Procesamos los autores (Google los da como Array, los convertimos a String)
    $autores = isset($info['authors']) ? implode(", ", $info['authors']) : 'Autor Desconocido';

    // 3. Extraemos los ISBNs recorriendo el sub-array industryIdentifiers
    $isbn10 = null;
    $isbn13 = null;
    if (isset($info['industryIdentifiers'])) {
        foreach ($info['industryIdentifiers'] as $id) {
            if ($id['type'] === 'ISBN_10') $isbn10 = $id['identifier'];
            if ($id['type'] === 'ISBN_13') $isbn13 = $id['identifier'];
        }
    }

    // 4. Limpiamos la descripción (Google a veces envía etiquetas HTML)
    $descripcion = isset($info['description']) ? strip_tags($info['description']) : null;

    // 5. Preparamos la sentencia SQL con nombres de parámetros (:param)
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
        $stmt = $pdo->prepare($sql);
        
        // 6. Ejecutamos pasando el array de datos
        return $stmt->execute([
            ':id'         => $item['id'], // El ID único de Google (ej: "zyTCAlS7fS8C")
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

?>