<?php

//traemos los archivos que necesitamos

require_once dirname(__DIR__).'/Core/Database.php';


try {

    $pdo = Database::getConnection();
    
    // 1. Obtener el texto enviado por POST (usando php://input para fetch)
    $input = json_decode(file_get_contents("php://input"), true);
    $texto = isset($input["libro"]) ? strtolower(trim($input["libro"])) : "";

    $sugerencias = array();

    if ($texto !== "") {

        // 2. Consulta a la tabla 'libros' (usamos LIKE para buscar coincidencias)
        // El comodín % al final busca libros que COMIENCEN con ese texto

        $stmt = $pdo->prepare("SELECT titulo FROM libros WHERE titulo LIKE ? LIMIT 10");
        $stmt->execute([$texto . '%']);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sugerencias[] = $row['titulo'];
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
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>