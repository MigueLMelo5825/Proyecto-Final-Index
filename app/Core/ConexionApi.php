<?php

function buscarEImportar($query, $pdo) {
    // Reemplaza con la clave que generaste en la consola de Google
    $apiKey = "AIzaSyAlkkQV1zi0_2WmtJV1l680lzw9moBat6g"; 
    
    // Construimos la URL de búsqueda (limitamos a 10 resultados para empezar)
    $url = "https://www.googleapis.com/books/v1/volumes?q=" . urlencode($query) . "&key=" . $apiKey . "&maxResults=10";

    // Intentamos obtener los datos
    $response = @file_get_contents($url);
    
    if ($response === false) {
        throw new Exception("Error al conectar con la API de Google Books.");
    }

    $data = json_decode($response, true);

    // Verificamos si Google encontró libros
    if (isset($data['items']) && count($data['items']) > 0) {
        $conteo = 0;
        foreach ($data['items'] as $item) {
            // Llamamos a la función de guardado que explicamos antes
            if (importarLibro($item, $pdo)) {
                $conteo++;
            }
        }
        return "Se importaron $conteo libros nuevos sobre '$query'.";
    } else {
        return "No se encontraron libros para la búsqueda: $query";
    }
}

?>