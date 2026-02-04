<?php
require_once __DIR__ . '/app/Core/autoload.php';
require_once __DIR__ . '/app/Core/Config.php';
require_once __DIR__ . '/app/Core/bGeneral.php';
require_once __DIR__ . '/app/libs/bSeguridad.php';

// Sesión segura
$session = new SessionManager(
    loginPage: 'index.php?ctl=inicio',
    timeout: 600
);

$session->checkSecurity();

// -------------------------------------------------------------
// Mapa de rutas
// -------------------------------------------------------------
$map = [
    'inicio' => [
        'controller' => 'InicioController',
        'action'     => 'inicio',
        'nivel'      => 1
    ],
    'perfil' => [
        'controller' => 'UsuarioController',
        'action'     => 'perfil',
        'nivel'      => 1
    ],
    'timeline' => [
        'controller' => 'TimelineController',
        'action'     => 'index',
        'nivel'      => 1
    ],
    'cargarPeliculas' => [
        'controller' => 'PeliculasController',
        'action'     => 'cargarPeliculas',
        'nivel'      => 1
    ],

    // ADMIN
    'panelAdmin' => [
        'controller' => 'AdminController',
        'action'     => 'index',
        'nivel'      => 3
    ],
    'cambiarRol' => [
        'controller' => 'AdminController',
        'action'     => 'cambiarRol',
        'nivel'      => 3
    ],
    'eliminarUsuario' => [
        'controller' => 'AdminController',
        'action'     => 'eliminarUsuario',
        'nivel'      => 3
    ],

    // LISTAS
    'crearLista' => [
    'controller' => 'ListaController',
    'action'     => 'crear',
    'nivel'      => 1
],
'añadirALista' => [
    'controller' => 'ListaController',
    'action'     => 'añadir',
    'nivel'      => 1
],
'registro' => [
    'controller' => 'UsuarioController',
    'action'     => 'registro',
    'nivel'      => 0
],



];

// -------------------------------------------------------------
// Resolución de ruta
// -------------------------------------------------------------
$ruta = $_GET['ctl'] ?? 'inicio';

if (!isset($map[$ruta])) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>Error 404: Ruta '$ruta' no encontrada</h1>";
    exit;
}

$controllerName = $map[$ruta]['controller'];
$actionName     = $map[$ruta]['action'];
$requiredLevel  = $map[$ruta]['nivel'];

// -------------------------------------------------------------
// Cargar el controlador AHORA (cuando ya sabemos cuál es)
// -------------------------------------------------------------
require_once __DIR__ . '/app/Controllers/' . $controllerName . '.php';

// -------------------------------------------------------------
// Comprobación de permisos
// -------------------------------------------------------------
if (!$session->hasLevel($requiredLevel)) {
    header("HTTP/1.0 403 Forbidden");
    echo "<h1>403: No tienes permisos para acceder a esta acción</h1>";
    exit;
}

// -------------------------------------------------------------
// Ejecución del controlador
// -------------------------------------------------------------
$controller = new $controllerName($session);

if (!method_exists($controller, $actionName)) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>Error 404: Acción '$actionName' no encontrada en $controllerName</h1>";
    exit;
}

$controller->$actionName();
