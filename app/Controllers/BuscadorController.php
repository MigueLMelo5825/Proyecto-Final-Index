<?php


/**
 * Ajustes realizados para mejorar la integración con el buscador del frontend.
 *
 * - Se reemplazó la lectura de datos por POST (php://input) por parámetros GET,
 *   ya que el buscador del header envía la consulta mediante URL (?texto=...).
 *   Esto evita que el backend reciba valores vacíos y garantiza compatibilidad
 *   con fetch() y peticiones AJAX simples.
 *
 * - Se unificó la salida para que siempre devuelva JSON limpio y consistente,
 *   eliminando modos alternativos que no se estaban utilizando.
 *
 * - Se normalizó el nombre del campo de imagen (imagen_url) tanto para libros
 *   como para películas, permitiendo que el frontend muestre la portada en el
 *   desplegable de sugerencias sin lógica adicional.
 *
 * - Se simplificó la estructura general del script para hacerlo más legible,
 *   predecible y fácil de mantener, manteniendo la misma funcionalidad original.
 */


require_once dirname(__DIR__) . '/Core/Database.php';

class BuscadorController {

    public function __construct($session) {
        // No necesitamos nada aquí
    }

    public function buscar() {
        try {
            $pdo = Database::getConnection();

            $texto = isset($_GET["texto"]) ? strtolower(trim($_GET["texto"])) : "";

            $sugerencias = [];

            if ($texto !== "") {

                $termino = '%' . $texto . '%';

                $stmt = $pdo->prepare("
                    SELECT id, titulo, autores AS info_extra, categoria AS genero, imagen_url, 'libro' AS tipo 
                    FROM libros 
                    WHERE titulo LIKE ? 

                    UNION 

                    SELECT id, titulo, anio AS info_extra, genero, portada AS imagen_url, 'pelicula' AS tipo 
                    FROM peliculas 
                    WHERE titulo LIKE ?

                    LIMIT 40
                ");

                $stmt->execute([$termino, $termino]);

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $sugerencias[] = $row;
                }
            }

            header("Content-Type: application/json");
            echo json_encode($sugerencias);
            exit;

        } catch (PDOException $e) {
            header("Content-Type: application/json");
            echo json_encode([
                "error" => true,
                "mensaje" => $e->getMessage()
            ]);
            exit;
        }
    }
}
