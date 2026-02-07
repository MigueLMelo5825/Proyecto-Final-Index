<?php
// ============================================================
// AUTOLOAD Y CONFIGURACIÓN GLOBAL
// ============================================================
require_once __DIR__ . '/app/Core/autoload.php';
require_once __DIR__ . '/app/Core/Config.php';
require_once __DIR__ . '/app/libs/bGeneral.php';
require_once __DIR__ . '/app/libs/bSeguridad.php';

// ============================================================
// MODELOS
// ============================================================ 
require_once __DIR__ . '/app/Models/ConexionApiLibros.php';
require_once __DIR__ . '/app/Models/ConexionPeliculasApi.php';
require_once __DIR__ . '/app/Models/Libros.php';
require_once __DIR__ . '/app/Models/ListaModel.php';
require_once __DIR__ . '/app/Models/Peliculas.php';
require_once __DIR__ . '/app/Models/TimelineModel.php';
require_once __DIR__ . '/app/Models/UsuarioModel.php';
require_once __DIR__ . '/app/Models/ListaItemsModel.php';



// ============================================================
// CORE (CONEXIONES A API, DB, ETC.)
// ============================================================
require_once __DIR__ . '/app/Core/autoload.php';
require_once __DIR__ . '/app/Core/Conexion.php';
require_once __DIR__ . '/app/Core/Config.php';
require_once __DIR__ . '/app/Core/Database.php';

// ============================================================
// CONTROLADORES
// ============================================================
require_once __DIR__ . '/app/Controllers/AdminController.php';
require_once __DIR__ . '/app/Controllers/BuscadorController.php';
require_once __DIR__ . '/app/Controllers/fichaLibroPeliculaController.php';
require_once __DIR__ . '/app/Controllers/InicioController.php';
require_once __DIR__ . '/app/Controllers/LibrosController.php';
require_once __DIR__ . '/app/Controllers/ListaController.php';
require_once __DIR__ . '/app/Controllers/PeliculasController.php';
require_once __DIR__ . '/app/Controllers/TimelineController.php';
require_once __DIR__ . '/app/Controllers/AdminController.php';
require_once __DIR__ . '/app/Controllers/UsuarioController.php';




// ============================================================
// SESIÓN SEGURA
// ============================================================
$session = new SessionManager(
    loginPage: 'index.php?ctl=login',
    timeout: 600
);

// ============================================================
// MAPA DE RUTAS
// ============================================================
$map = [
    'inicio' => [
        'controller' => 'InicioController',
        'action'     => 'inicio',
        'nivel'      => 0
    ],

    'login' => [
        'controller' => 'UsuarioController',
        'action'     => 'login',
        'nivel'      => 0
    ],

    'registro' => [
        'controller' => 'UsuarioController',
        'action'     => 'registro',
        'nivel'      => 0
    ],

    
    'recupero' => [
        'controller' => 'UsuarioController',
        'action'     => 'recuperar',
        'nivel'      => 0
    ],

    'reset' => [
        'controller' => 'UsuarioController',
        'action'     => 'reset',
        'nivel'      => 0
    ],

    'buscar' => [
        'controller' => 'BuscadorController',
        'action'     => 'buscar',
        'nivel'      => 0
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

    'fichaLibroPelicula' => [
        'controller' => 'fichaLibroPeliculaController',
        'action'     => 'ficha',
        'nivel'      => 1
    ],

    'guardarLikeYComentario' => [
        'controller' => 'fichaLibroPeliculaController',
        'action'     => 'guardarLikesYCalificacion',
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

    // IMPORTACIÓN DESDE API
    'importarPeliculas' => [
        'controller' => 'ImportarPeliculasController',
        'action'     => 'importar',
        'nivel'      => 3
    ],

    'importarLibros' => [
        'controller' => 'ImportarLibrosController',
        'action'     => 'importar',
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
    'verLista' => [
    'controller' => 'ListaController',
    'action'     => 'ver',
    'nivel'      => 1
],

'agregarItem' => [
    'controller' => 'ListaController',
    'action'     => 'agregarItem',
    'nivel'      => 1
],

];

// ============================================================
// RESOLUCIÓN DE RUTA
// ============================================================
$ruta = $_GET['ctl'] ?? 'inicio';

if (!isset($map[$ruta])) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>Error 404: Ruta '$ruta' no encontrada</h1>";
    exit;
}

$controllerName = $map[$ruta]['controller'];
$actionName     = $map[$ruta]['action'];
$requiredLevel  = $map[$ruta]['nivel'];

// ============================================================
// SEGURIDAD
// ============================================================
if (!in_array($ruta, ['login', 'registro', 'recupero', 'reset', 'inicio'])) {
    $session->checkSecurity();
}

// ============================================================
// COMPROBACIÓN DE PERMISOS
// ============================================================
if (!$session->hasLevel($requiredLevel)) {
    header("HTTP/1.0 403 Forbidden");
    echo "<h1>403: No tienes permisos para acceder a esta acción</h1>";
    exit;
}

// ============================================================
// EJECUCIÓN DEL CONTROLADOR
// ============================================================
$controller = new $controllerName($session);

if (!method_exists($controller, $actionName)) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>Error 404: Acción '$actionName' no encontrada en $controllerName</h1>";
    exit;
}

$controller->$actionName();
