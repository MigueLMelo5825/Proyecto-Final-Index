<?php

// Cargar controladores
require_once __DIR__ . '/app/Controllers/UsuarioController.php';
require_once __DIR__ . '/app/Controllers/PeliculasController.php';
require_once __DIR__ . '/app/Controllers/LibrosController.php'; // si lo usas

// Obtener parámetros de la URL
$controllerName = $_GET['controller'] ?? 'usuario';
$action = $_GET['action'] ?? 'timeline'; // acción por defecto

// Normalizar nombre del controlador
$controllerName = strtolower($controllerName);

// Crear instancia del controlador correspondiente
switch ($controllerName) {
    case 'usuario':
        $controller = new UsuarioController();
        break;

    case 'peliculas':
        $controller = new PeliculasController();
        break;

    case 'libros':
        $controller = new LibrosController();
        break;

    default:
        die(" Controlador '$controllerName' no encontrado.");
}

// Verificar que la acción existe
if (!method_exists($controller, $action)) {
    die(" Acción '$action' no encontrada en el controlador '$controllerName'.");
}

// Ejecutar acción
$controller->$action();
