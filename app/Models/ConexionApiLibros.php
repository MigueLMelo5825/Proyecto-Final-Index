<?php

class ConexionApiLibros
{
    public static function buscarEImportar($query, $pdo)
    {
        // Tu API key de Google Books
        $apiKey = "AIzaSyAlkkQV1zi0_2WmtJV1l680lzw9moBat6g";

        // URL optimizada para obtener libros modernos y con metadatos completos
        $url = "https://www.googleapis.com/books/v1/volumes?q="
            . urlencode($query . " after:2000")
            . "&key=" . $apiKey
            . "&maxResults=500"
            . "&printType=books"
            . "&orderBy=relevance"
            . "&filter=paid-ebooks";

        // Llamada a la API
        $response = file_get_contents($url);

        if ($response === false) {
            throw new Exception("Error al conectar con la API de Google Books.");
        }

        $data = json_decode($response, true);

        // Si no hay resultados
        if (!isset($data['items']) || count($data['items']) === 0) {
            return "No se encontraron libros para '$query'.";
        }

        // Usamos tu modelo Libros
        $librosModel = new Libros();
        $librosModel->guardarLibros($data['items'], 20);

        return "Se importaron libros sobre '$query'.";
    }
}
