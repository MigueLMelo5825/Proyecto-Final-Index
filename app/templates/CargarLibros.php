<?php

require_once dirname(__DIR__).'/Core/Database.php';
require_once dirname(__DIR__).'/Core/ConexionApiLibros.php';

try {
    $pdo = Database::getConnection();

    $temas = [
        "futbol", "guerra", "roma", "dioses", "olimpo", 
        "dragones"
    ];

    echo "<h2>Iniciando Importación Masiva</h2>";
    echo "<div style='font-family: sans-serif; padding: 10px; background: #f4f4f4;'>";

    foreach($temas as $t) {
        // Ejecutamos la importación y mostramos el resultado de cada tema
        $res = ConexionApiLibros::buscarEImportar($t, $pdo);
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