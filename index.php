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
require_once __DIR__ . '/app/Models/SeguidorModel.php';

// ============================================================
// CORE
// ============================================================
require_once __DIR__ . '/app/Core/Conexion.php';
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
require_once __DIR__ . '/app/Controllers/UsuarioController.php';

require_once __DIR__ . './app/libs/ejemploPHPMailer/PHPMailer/src/Exception.php';
require_once __DIR__ . './app/libs/ejemploPHPMailer/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . './app/libs/ejemploPHPMailer/PHPMailer/src/SMTP.php';


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

    'activar' => [
        'controller' => 'UsuarioController',
        'action'     => 'activar',
        'nivel'      => 0
    ],

    'reset' => [
        'controller' => 'UsuarioController',
        'action'     => 'reset',
        'nivel'      => 0
    ],
    'logout' => [
    'controller' => 'UsuarioController',
    'action'     => 'logout',
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

    'seguir' => [
    'controller' => 'UsuarioController',
    'action'     => 'seguir',
    'nivel'      => 1
],

'dejarseguir' => [
    'controller' => 'UsuarioController',
    'action'     => 'dejarSeguir',
    'nivel'      => 1
],

'verSeguidores' => [
    'controller' => 'UsuarioController',
    'action'     => 'verSeguidores',
    'nivel'      => 1
],

'verSeguidos' => [
    'controller' => 'UsuarioController',
    'action'     => 'verSeguidos',
    'nivel'      => 1
],

'buscarUsuarios' => [
    'controller' => 'UsuarioController',
    'action'     => 'buscarUsuarios',
    'nivel'      => 1
],


    'timeline' => [
        'controller' => 'TimelineController',
        'action'     => 'mostrar',
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

    'guardarLikesYCalificacion' => [
        'controller' => 'fichaLibroPeliculaController',
        'action'     => 'guardarLikesYCalificacion',
        'nivel'      => 1
    ],

    // AJUSTES DE PERFIL
    'ajustesPerfil' => ['controller' => 'UsuarioController', 'action' => 'ajustesPerfil', 'nivel' => 1],
    'guardarFotoPerfil' => ['controller' => 'UsuarioController', 'action' => 'guardarFotoPerfil', 'nivel' => 1],
    'guardarBio' => ['controller' => 'UsuarioController', 'action' => 'guardarBio', 'nivel' => 1],
    'guardarTopLibros' => ['controller' => 'UsuarioController', 'action' => 'guardarTopLibros', 'nivel' => 1],
    'guardarTopPeliculas' => ['controller' => 'UsuarioController', 'action' => 'guardarTopPeliculas', 'nivel' => 1],

    'guardarComentario' => [
        'controller' => 'fichaLibroPeliculaController',
        'action'     => 'guardarComentario',
        'nivel'      => 1
    ],

    'eliminarComentario' => [
        'controller' => 'fichaLibroPeliculaController',
        'action'     => 'eliminarComentario',
        'nivel'      => 1
    ],

    // LISTAS
    'crearLista' => ['controller' => 'ListaController', 'action' => 'crear', 'nivel' => 1],
    'anadirALista' => ['controller' => 'ListaController', 'action' => 'anadir', 'nivel' => 1],
    'verLista' => ['controller' => 'ListaController', 'action' => 'ver', 'nivel' => 1],

    // FICHA LIBRO/PELÍCULA
    'ficha' => ['controller' => 'fichaLibroPeliculaController', 'action' => 'index', 'nivel' => 0],

    // ADMIN
    'panelAdmin' => ['controller' => 'AdminController', 'action' => 'index', 'nivel' => 3],
    'cambiarRol' => ['controller' => 'AdminController', 'action' => 'cambiarRol', 'nivel' => 3],
    'eliminarUsuario' => ['controller' => 'AdminController', 'action' => 'eliminarUsuario', 'nivel' => 3],

    // IMPORTACIÓN
    'importarPeliculas' => ['controller' => 'ImportarPeliculasController', 'action' => 'importar', 'nivel' => 3],
    'importarLibros' => ['controller' => 'ImportarLibrosController', 'action' => 'importar', 'nivel' => 3],
];



// ============================================================
// RESOLUCIÓN DE RUTA
// ============================================================
$ruta = $_GET['ctl'] ?? 'inicio';

if (!isset($map[$ruta])) {
    die("<h1>Error 404: Ruta '$ruta' no encontrada</h1>");
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

if (!$session->hasLevel($requiredLevel)) {
    die("<h1>403: No tienes permisos</h1>");
}

// ============================================================
// EJECUCIÓN DEL CONTROLADOR
// ============================================================
$controller = new $controllerName($session);

if (!method_exists($controller, $actionName)) {
    die("<h1>Error 404: Acción '$actionName' no encontrada en $controllerName</h1>");
}

$controller->$actionName();
