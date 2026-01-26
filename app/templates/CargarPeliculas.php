<?php
require_once dirname(__DIR__).'/Models/Peliculas.php';
require_once dirname(__DIR__).'/Core/Database.php';
    
$pdo = Database::getConnection();

if (importarPeliculasTMDB($pdo, 20)) {
    echo "Películas importadas correctamente.";
} else {
    echo "Error al importar películas.";
}
