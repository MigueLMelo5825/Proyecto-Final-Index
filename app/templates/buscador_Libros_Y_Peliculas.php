<?php

//añadimos el archivo Database

require_once dirname(__DIR__) . '/Core/Database.php';

try {

    $pdo = Database::getConnection();

    // 1. Obtener el texto enviado por POST (usando php://input para fetch)
    $input = json_decode(file_get_contents("php://input"), true);
    $texto = isset($input["inputBuscador"]) ? strtolower(trim($input["inputBuscador"])) : "";

    $sugerencias = array();

    $termino = '%' . $texto . '%';

    if ($texto !== "") {

        // 2. Consulta a la tabla 'libros' (usamos LIKE para buscar coincidencias)
        // El comodín % % para buscar cualquier libro relacionadas al texto

        $stmt = $pdo->prepare("SELECT id, titulo, autores AS info_extra, categoria AS genero, imagen_url, 'libro' AS tipo 
                                FROM libros 
                                WHERE titulo LIKE ? 
                                UNION 
                                SELECT id, titulo, anio AS info_extra, genero, portada AS imagen_url, 'pelicula' AS tipo 
                                FROM peliculas 
                                WHERE titulo LIKE ? 
                                LIMIT 40");
        $stmt->execute([$termino, $termino]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sugerencias[] = $row;
        }
    }

    // 3. Determinar modo de salida (por defecto json)
    $modo = $_GET["modo"] ?? "json";

    if ($modo == "ul") {
        if (count($sugerencias) > 0) {
            echo "<ul>\n<li>" . implode("</li>\n<li>", $sugerencias) . "</li>\n</ul>";
        } else {
            echo "<ul></ul>";
        }
    } else {
        header("Content-Type: application/json");
        echo json_encode($sugerencias);
        exit;
    }
} catch (PDOException $e) {
    header("Content-Type: application/json");
    echo json_encode([
        "error" => true,
        "mensaje" => $e->getMessage()
    ]);
    exit;
}
