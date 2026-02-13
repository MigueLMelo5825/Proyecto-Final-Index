<?php
require_once "app/Core/Database.php";
require_once "app/Models/ConexionApiLibros.php";

$pdo = Database::getConnection();

// Cambia la búsqueda por lo que quieras cargar
echo ConexionApiLibros::buscarEImportar("fantasía", $pdo);
