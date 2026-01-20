<?php

namespace Controllers;

use Core\ConexionApi;
use Models\Libros;
use Database;

class LibroController {

    public function importarDesdeGoogle($termino) {
        try {
            // 1. Obtener la conexión PDO (Singleton)
            $db = \Database::getConnection();

            // 2. Instanciar la lógica de la API
            $api = new \Core\ConexionApi();
            
            echo "Buscando libros sobre: " . htmlspecialchars($termino) . "...<br>";

            // 3. Obtener los datos crudos de Google
            $librosGoogle = $api->obtenerDatosDeGoogle($termino);

            if (!$librosGoogle) {
                echo "No se encontraron resultados en la API.";
                return;
            }

            // 4. Procesar e Insertar usando el Modelo
            $modeloLibro = new \Models\Libros();
            $contador = 0;

            foreach ($librosGoogle as $item) {
                if ($modeloLibro->importarLibro($item, $db)) {
                    $contador++;
                }
            }

            echo "Proceso finalizado. Se han guardado $contador libros nuevos.";

        } catch (\Exception $e) {
            echo "Error en el controlador: " . $e->getMessage();
        }
    }
}