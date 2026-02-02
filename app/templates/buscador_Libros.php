<?php

//añadimos el archivo Database

require_once dirname(__DIR__).'/Core/Database.php';

try {

    $pdo = Database::getConnection();
    
    // 1. Obtener el texto enviado por POST (usando php://input para fetch)
    $input = json_decode(file_get_contents("php://input"), true);
    $texto = isset($input["libro"]) ? strtolower(trim($input["libro"])) : "";

    $sugerencias = array();

    if ($texto !== "") {

        // 2. Consulta a la tabla 'libros' (usamos LIKE para buscar coincidencias)
        // El comodín % % para buscar cualquier libro o pelicula relacionadas al texto

        $stmt = $pdo->prepare("SELECT id, titulo, autores, categoria, imagen_url FROM libros WHERE titulo LIKE ? LIMIT 20");
        $stmt->execute(['%' . $texto . '%']);
        
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
?>