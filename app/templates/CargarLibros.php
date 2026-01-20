<?php
// Usamos require_once para asegurar que los archivos se carguen correctamente
require_once dirname(__DIR__).'/Core/Database.php';
require_once dirname(__DIR__).'/Core/ConexionApi.php';

try {
    $pdo = Database::getConnection();

    $temas = [
        "Cocina", "Historia", "Tecnología", "Deportes", "Ciencia Ficcion", 
        "Aventura", "Fantasias", "Misterio", "No ficcion", "Terror",
        "Documentales", "Crimenes", "Romantico", "Humor", "Juvenil",
        "Juegos","Crepusculo"
    ];

    echo "<h2>Iniciando Importación Masiva</h2>";
    echo "<div style='font-family: sans-serif; padding: 10px; background: #f4f4f4;'>";

    foreach($temas as $t) {
        // Ejecutamos la importación y mostramos el resultado de cada tema
        $res = buscarEImportar($t, $pdo);
        echo "<strong>Tema: $t</strong> -> $res <br>";
        
        // OPCIONAL: Pausa de 0.5 segundos para no saturar la API de Google
        usleep(500000); 
    }

    echo "</div>";
    echo "<p style='color: green; font-weight: bold;'>¡Proceso de importación finalizado con éxito!</p>";
    echo "<p>Ya puedes revisar la tabla 'libros' en tu base de datos.</p>";

} catch (Exception $e) {
    // Es mejor mostrar el error en pantalla mientras estás desarrollando
    echo "<p style='color: red;'>Error crítico: " . $e->getMessage() . "</p>";
    error_log($e->getMessage());
}